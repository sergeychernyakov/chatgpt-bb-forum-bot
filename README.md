Sure! Here's the updated `README.md` file based on the changes to the plugin code:

<p align="center">
<a href="https://www.buddyboss.com/"><img src="https://github.com/buddyboss/chatgpt-bb-forum-bot/raw/master/.github/buddyboss-logo.jpg" alt="BuddyBoss" width="288" height="93" /></a>
</p>

# ChatGPT BB Forum Bot Plugin

Welcome to the **ChatGPT BB Forum Bot Plugin** repository on GitHub. The ChatGPT BB Forum Bot Plugin is designed to automatically respond to new member introductions and discussion replies in your BuddyBoss/BuddyPress forums using OpenAI's GPT-4 API. This plugin helps create a more engaging and interactive community experience.

## Features

- Automatically replies to new member introductions.
- Automatically replies to new discussion posts and replies.
- Customizable ChatGPT prompts for each user.
- Easy integration with BuddyBoss/BuddyPress platforms.
- Includes context from parent replies for more coherent responses.

## Installation

### Prerequisites

- WordPress
- BuddyBoss Platform or BuddyPress
- MAMP or any local development environment with PHP and MySQL

### Steps

1. **Download the Plugin:**
   Download the plugin files from the GitHub repository.

2. **Upload the Plugin:**
   Upload the plugin files to the `/wp-content/plugins/chatgpt-bb-forum-bot` directory.

3. **Activate the Plugin:**
   Go to the WordPress admin dashboard, navigate to Plugins, and activate the ChatGPT BB Forum Bot Plugin.

4. **Configure the Plugin:**
   - Go to the plugin settings page.
   - Enter your OpenAI API key.

## Configuration

### OpenAI API Key

To use this plugin, you need an OpenAI API key. You can obtain this by signing up on the OpenAI platform.

### Custom User Prompts

Users can set their own custom ChatGPT prompts in their profile settings. This allows for personalized responses based on user-defined prompts.

1. Go to the user profile page.
2. Find the "ChatGPT Prompt" field.
3. Enter a custom prompt and save the changes.

## Usage

### Responding to New Introductions

The plugin listens for new introduction posts and automatically generates a reply using the configured ChatGPT prompt for each user.

### Responding to Discussion Posts and Replies

The plugin also listens for new discussion posts and replies, generating responses to keep the conversation active and engaging. It includes context from parent replies to ensure more coherent and relevant responses.

## Development

### Setting Up for Development

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/sergeychernyakov/chatgpt-bb-forum-bot.git
   ```

2. **Navigate to the Plugin Directory:**
   ```bash
   cd chatgpt-bb-forum-bot
   ```

3. **Install Dependencies:**
   If there are any dependencies, install them using Composer or NPM.

### Debugging with MAMP

1. **Enable Debugging in WordPress:**
   Add the following to your `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ini_set('display_errors', 1);
   ```

2. **View Log Files:**
   The log file can be found at `/wp-content/debug.log`.

## Contributing

We welcome contributions to the ChatGPT BB Forum Bot Plugin! If you have any suggestions or improvements, feel free to submit a pull request or open an issue on GitHub.

### Code of Conduct

Please read our [Code of Conduct](CODE_OF_CONDUCT.md) before contributing.

## License

This plugin is licensed under the GPLv3. See the [LICENSE](LICENSE.txt) file for more details.

## Documentation

- [Developer Wiki](https://github.com/buddyboss/chatgpt-bb-forum-bot/wiki)
- [BuddyBoss Platform](https://github.com/buddyboss/buddyboss-platform)

## Contact

For any queries or support, please contact [Sergey Chernyakov](https://github.com/sergeychernyakov).

<p align="center">
<a href="https://www.buddyboss.com/"><img src="https://github.com/buddyboss/chatgpt-bb-forum-bot/raw/master/.github/buddyboss-logo.jpg" alt="BuddyBoss" width="288" height="93" /></a>
</p>


Plan:
+ vary the number of bot replies to 2 - 4 for each post
add settings for that

+ check replies

- When a Member posts in the forum, other Members have the ability to 'Like' the post, by clicking the thumbs up icon (see attached screenshot). I use a plugin called WP ULike to enable the thumbs up/like functionality.


http://localhost:8888/wp-login.php
chernyakov.sergey@gmail.com
$P$BwVf64sNiLBy
