# 🧩 Joomla! Workflow Plugin — Category Assigner

This Joomla! workflow plugin allows administrators to control article category assignment during workflow transitions.

## 📦 Installation

1. Download the latest ZIP via “Download ZIP” button.
2. In your Joomla admin, go to **System > Install > Extensions**.
3. Upload and install the ZIP file.

## ⚙️ Configuration

After installation:

1. Go to **System > Plugins**.
2. Search for **Category Assigner**.
3. Enable the plugin.
4. The default category for a new article can be set on plugin configuration page under **Default Category** field. If not set, "uncategorised" will be the default one.

## 🧩 Features

- 🔒 Category field disabled in article form to avoid manual changes.
- 📂 Default category automatically assigned on article creation.
- 🔄 Category changes dynamically during workflow transitions.

## 🚀 How to Test the Plugin

Follow these steps to see the plugin in action:

### 1. Create Categories

Go to **Content > Categories** and create:

- 📝 Draft
- 📝 In Review
- 📝 Published

### 2. Configure the Plugin

Go to **System > Plugins > Category Assign**

- Set **Default Category:** `Draft`
- Save & close.

> **Tip:** This ensures all new articles start in the **Draft** category.

### 3. Create a Workflow

Go to **Content > Workflows** and create:

- **Name:** `Article Publishing Workflow`
- **Default Stage:** `Basic Stage`  
  _(For simplicity / testing purposes, we'll stick with only the Basic Stage.)_

- Set this workflow as **Default**.

### 4. Add Workflow Transitions

Go to **Content > Workflows > Manage Transitions**, and add:

#### ➡️ Send to Review
- **From:** All
- **To:** Basic Stage
- **Options > Category:** In Review
- **Publishing State:** Unpublished

#### 🚀 Publish Article
- **From:** All
- **To:** Basic Stage
- **Options > Category:** Published
- **Publishing State:** Published

#### 🔄 Move to Draft
- **From:** All
- **To:** Basic Stage
- **Options > Category:** Draft
- **Publishing State:** Unpublished

> **Note:** All transitions stay within **Basic Stage** — only categories and publishing states are updated.

### 5. Create an Article

Go to **Content > Articles > New**

- Create a new article.
- Keep the workflow stage at **Basic Stage**.
- Save the article.

> **Expected Result:**  
> - Category field is disabled.
> - Article is automatically assigned to **Draft** category after the article save.

### 6. Run Workflow Transitions

Go to **Content > Articles**, Click the **Stage** column cell of article:

1. **Send to Review** → Article moves to **In Review** category.
2. **Publish Article** → Article moves to **Published** category.
3. **Move to Draft** → Article moves back to **Draft** category.

---

## 💡 Development Notes

- Plugin developed with Joomla 4.4.12.
- Language files included only for en-GB.

## 📝 License

This project is licensed under the MIT License.