# WordPress symfony form integration plugin
### Installation
```sh
php composer.phar require sunnyct-wp/sunnyct-wp-forms:^1.0
```
### Example form
```text
[text name="your_email" label="Your email"]
[textarea name="your_message" label="Your message"]
[validate name="your_message" type="length" min="10"]
[validate name="your_message" type="required"]
[button type="submit" name="submit" label="Send"]
[action type="success_message"]
	Welcome
[/action]
[action type="auto_hide" delay="3"]
```
