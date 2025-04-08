<?php

namespace Joomla\Plugin\Workflow\CategoryAssigner\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\CMS\Workflow\WorkflowPluginTrait;

use Joomla\CMS\Event\Workflow\WorkflowTransitionEvent;
use Joomla\Event\Event;
use Joomla\CMS\Form\Form;
use stdClass;

/**
 * Plugin to assign categories to articles during workflow transitions.
 */
final class CategoryAssigner extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;
    use WorkflowPluginTrait;

    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     * 
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepareForm' => 'onContentPrepareForm',
            'onUserBeforeDataValidation' => 'onUserBeforeDataValidation',
            'onWorkflowBeforeTransition' => 'onWorkflowBeforeTransition'
        ];
    }

    /**
     * Prepares and modifies the category field in the article form and
     * enhances the workflow transition form.
     *
     * @param Event $event
     * @return void
     */
    public function onContentPrepareForm(Event $event) : void
    {
        /** @var Form $form **/
        $form = $event->getArgument(0);
        /** @var stdClass|array $data **/
        $data = $event->getArgument(1);

        $context = $form->getName();
        if ($context === 'com_content.article') {
            $form->setFieldAttribute('catid', 'disabled', 'true');
            $form->setFieldAttribute('catid', 'required', 'false');
        } else if ($context === 'com_workflow.transition') {
            $this->enhanceWorkflowTransitionForm($form, $data);
        }
    }

    /**
     * Modifies the new article category and 
     * enables the category field before content validation to
     * prevent invalid field error.
     * 
     * @param Event $event
     * @return bool
     */
    public function onUserBeforeDataValidation(Event $event): bool
    {
        /** @var Form $form **/
        $form = $event->getArgument(0);
        /** @var stdClass|array $data **/
        $data = $event->getArgument(1);
        
        $context = $form->getName();
        if ($context === 'com_content.article') {
            $data['catid'] = $this->params->get('default_category', 2);
            $event->setArgument(1, $data);
            $form->setFieldAttribute('catid', 'disabled', 'false');
            $event->setArgument(0, $form);
        }

        return true;
    }

    /**
     * Assigns the selected category to the article before 
     * executing the workflow transition.
     * 
     * @param WorkflowTransitionEvent $event
     * @return bool
     */
    public function onWorkflowBeforeTransition(WorkflowTransitionEvent $event): bool
    {
        /** @var stdClass $transition **/
        $transition = $event->getArgument('transition');
        /** @var array $pks **/
        $pks = $event->getArgument('pks');

        $articleId = (int) reset($pks);
        $categoryId = (int) $transition->options->get('category_id');
        if ($categoryId && $articleId) {
            $this->assignCategoryToArticle($articleId, $categoryId);
        }

        return true;
    }

    /**
     * Check if the current plugin should execute 
     * workflow related activities
     *
     * @param string $context
     * @return bool
     */
    protected function isSupported(string $context): bool
    {
        return true;
    }

    /**
     * Assign category to the article.
     *
     * @param int $articleId
     * @param int $categoryId
     * @return void
     */
    public function assignCategoryToArticle(int $articleId, int $categoryId): void
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
                    ->update('#__content')
                    ->set('catid = ' . (int) $categoryId)
                    ->where('id = ' . (int) $articleId);
        $db->setQuery($query);
        $db->execute();
    }
}