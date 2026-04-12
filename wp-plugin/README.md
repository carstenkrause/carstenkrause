# CDO Times WordPress Plugin

This folder contains a sample WordPress plugin that registers a custom post type for fractional executives and integrates with an AI service to generate resumes.

## Setup

1. Copy the `cdotimes-fractional-executives` folder to your WordPress `wp-content/plugins` directory.
2. Define the constant `CDT_AI_ENDPOINT` in your `wp-config.php` with the URL of the AI service that returns resume text:

```php
define('CDT_AI_ENDPOINT', 'https://your-api-endpoint.example.com');
```

3. Activate the plugin in the WordPress admin.

## Usage

- Use the shortcode `[cdt_executive_list]` to display a searchable list of executives.
- Use the shortcode `[cdt_ai_resume id="123"]` to display an AI-generated resume for the executive post with ID `123`. When placed inside an executive post, the `id` attribute can be omitted.

The plugin expects the executive details to be stored in the post content. This content is sent to the AI endpoint to create the resume.
