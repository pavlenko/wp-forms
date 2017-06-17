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
## Form shortcode `[sunnyct-wp-forms ...]`
For insert form on page use shortcode `[sunnyct-wp-forms id="123"]`
where `123` is identifier of form 

## Fields
Common attributes
- name - string, required

Text field attributes
- type - string, possible values: text (default), search, email, password
- label - string
- placeholder - string

> Example: `[text ...]`

Textarea field attributes
- label - string
- placeholder - string

> Example: `[textarea ...]`

Checkbox field attributes
- label - string

> Example: `[checkbox ...]`

Choice block attributes
- type - string, possible values: select (default), checkboxes, radios
- label - string
- multiple - boolean, useful only for type="select"

> Example: `[choice_block multiple="1" ...] choice options here [/choice_block]` 1=true, 0=false

Choice option attributes
 - name - string, required if label empty
 - label - string, required if name empty
 - group - string, options group title
 
> Example: `[choice_option name="foo" label="Foo" group="Examples"]`

Button attributes
- type - string, possible values: button (default), submit, reset
- label - string

> Example 1: `[button label="Text label here" ...]`

> Example 2: `[button] HTML label here [/button]`

## Validate shortcode `[validate ...]`
Common attributes
 - name - string, required, field name to validate
 - type - string, required, possible values: length, required, email

For `type="required"` no more attributes needed

> Example: `[validate name="title" type="required"]`

For `type="length"` attributes
 - min - integer, required only if max empty
 - max - integer, required only if min empty

> Example: `[validate name="title" type="length" min="3"]`

For `type="email"` attributes
 - checkMX - boolean
 - checkHost - boolean
 - strict - boolean

> Example: `[validate name="title" type="email" strict="1"]` 1=true, 0=false

## Action shortcode `[action ...]`
TODO