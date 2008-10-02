<?php
/*
* Object-Oriented stuff for Plans!
* Defines a set of objects that are constructed by pages and then passed
* to the interface, where they are turned into HTML or whatever else.
*
* Note: Some of the objects implement a toHTML() method.  This is for
* convenience only, and interfaces are by no means required to make use
* of these methods.
*/
/**
 * PlansPage
 * This is an entire page of content generated by plans. Passed whole to the
 * interface for printing.
 */
class PlansPage
{
	/* what category this page belongs to */
	public $group;
	/* a unique identifier for this page */
	public $identifier = 'defaultpage';
	/* A title for the page */
	public $title = PLANSVNAME;
	/* a MainPanel that contains all the links, autoreads, etc */
	public $mainpanel = NULL;
	/* an array of Widgets, the contents of this page */
	public $contents = array();
	/* a Footer object */
	public $footer;
	/* the stylesheet to use - a URL as a string */
	public $stylesheet;
	/* TODO needed? */
	public $searchname;
	/* the URL of this page */
	public $url;
	/* the current priority level of the autoread list */
	public $autoreadpriority;
	function __construct($_group, $_id, $_title, $_url) 
	{
		$this->group = $_group;
		$this->identifier = $_id;
		$this->title = $_title;
		$this->url = $_url;
	}
	public function append(Widget&$widge) 
	{
		$this->contents[] = $widge;
	}
}
/**
 * The main panel of links for plans. Includes the finger box, links to different
 * parts of Plans, and the autoread lists.
 */
class MainPanel
{
	public $identifier = 'mainpanel';
	/* a Hyperlink that links back to the home page */
	public $linkhome;
	/* a Form that gives the Finger (or Read) box */
	public $fingerbox;
	/* an array of the basic links */
	public $requiredlinks;
	/* an array of optional links */
	public $optionallinks;
	/* an array of AutoRead objects */
	public $autoreads;
}
/**
 * The footer found on most pages.  Currently may contain the
 * "do you read?" thing and legalese stuff.
 */
class Footer
{
	/* a PlanLink to a recently updated plan */
	public $doyouread;
	/* whatever legalese text goes at the bottom */
	public $legal; //TODO currently InfoText, does it need its own class?
	
}
/**
 * General-purpose class for things found within a PlansPage.
 * Should not actually be used, just exists to be extended by other classes.
 */
class Widget
{
	/* a unique identifier for this item */
	public $identifier;
	/* A title for the page - may be NULL */
	public $title = 'A Widget';
	/* Optional - if we desire additional attributes in the tag when we
	convert to HTML, this field serves as a convenience.  Should only
	be set by the interface. */
	public $html_attributes;
	function __construct($_id, $_title) 
	{
		$this->identifier = $_id;
		$this->title = $_title;
	}
	function toHTML() 
	{
		/* we should never get this high, return a warning */
		return "Warning: toHTML() has been called on an object that " . "does not provide it. Please report this as a bug.";
	}
}
/**
 * A list of widgets somehow thematically related. May serve as a structural sub-category.
 */
class WidgetGroup extends Widget
{
	/* An array of widgets */
	public $contents;
	function __construct($identifier, $title) 
	{
		parent::__construct($identifier, $title);
		$this->contents = array();
	}
	//TODO pass by reference? here and in PlansPage
	public function append(Widget $widge) 
	{
		$this->contents[] = &$widge;
	}
	public function toHTML($callback = null) 
	{
		$str = '<!--WidgetList start-->';
		foreach($this->contents as $item) {
			if ($callback == null) {
				$str = $str . "\n" . $item->toHTML();
			} else {
				$str.= $callback($item);
			}
		}
		$str = $str . "\n" . '<!--WidgetList end-->';
		return $str;
	}
}
// TODO is this a good distinction?
class WidgetList extends WidgetGroup
{
}
/**
 * An autoread list. Stores its own priority level.
 */
class AutoRead extends WidgetList
{
	/* a number designating the "priority" level */
	public $priority;
	// $contents contains PlanLinks
	function __construct($p) 
	{
		parent::__construct("autoreadlev$p", "Level $p");
		$this->priority = $p;
		$this->contents = array();
	}
}
/**
 * A link to another page.
 */
class Hyperlink extends Widget
{
	/* location of the other page */
	public $href;
	/* the text of the link */
	public $description;
	function __construct($_id, $_href, $_desc) 
	{
		parent::__construct($_id, NULL);
		$this->href = $_href;
		$this->description = $_desc;
	}
	function toHTML() 
	{
		return "<a href=\"$this->href\"$this->html_attributes>$this->description</a>";
	}
}
/**
 * A link to another user's plan.
 */
class PlanLink extends Hyperlink
{
	function __construct($_id, $username) 
	{
		parent::__construct($_id, NULL, $username);
		$this->href = "read.php?searchname=$username"; //TODO hmmm...
		
	}
}
/**
 * A web form.
 */
class Form extends WidgetGroup
{
	/* These constants apply to XForms or something. Avram knows. */
	const FIELD_NUMERIC = 10;
	const FIELD_TEXT = 15;
	/* basic settings for the form */
	public $action;
	public $method;
	/* an array containing the fields in the form, as FormItems */
	public $fields;
	public function __construct($identifier, $title) 
	{
		parent::__construct($identifier, $title);
		$this->contents = array();
	}
	// DEPRECATED
	public function appendField(FormItem $f) 
	{
		return $this->append($f);
	}
	public function toHTML($callback = null) 
	{
		$str = '<form method="' . $this->method . '" action="' . $this->action . '">';
		$str.= parent::toHTML($callback);
		$str.= "\n</form>";
		return $str;
	}
}
/**
 * An item (field) within a form. May also be other Widgets inside a form.
 */
class FormItem extends Widget
{
	/* $type may be one of the following strings:
	* radio, checkbox, hidden, textarea
	* If $type is 'widget', $value points to a Widget object which contains
	* the widget. This type allows other types of info to exist inside forms.
	*/
	public $type;
	/* the name of the field */
	public $name;
	/* a constant as defined in the Form class */
	public $datatype;
	/* text description of the item */
	public $description;
	/* the value of the item */
	public $value;
	/* Is it checked (checkboxes and radio buttons)? */
	public $checked;
	public function __construct($_type, $_name, $_value = null) 
	{
		$this->type = $_type;
		$this->name = $_name;
		$this->id = $_name;
		$this->value = $_value;
		$this->description = NULL;
		$this->identifier = NULL;
	}
	public function toHTML() 
	{
		$str = '';
		switch ($this->type) {
			case 'widget':
				$str = $this->value->toHTML();
				break;

			case 'textarea':
				$str = $str . "<textarea name=\"$this->name\">" . $this->description . '</textarea>';
				break;

			case 'radio':
			case 'checkbox':
				$str = $str . "<input type=\"$this->type\" name=\"$this->name\"" . "value=\"$this->value\"" . (($this->checked) ? ' checked' : '') . ">$this->description";
				break;

			default:
				$str = $str . "<input type=\"$this->type\"";
				if ($this->name) $str.= " name=\"$this->name\"";
				$str.= " value=\"$this->value\">$this->description";
				break;
			}
			return $str;
	}
}
class EditBox extends Form
{
	public $username;
	public $text; // A PlanText object
	public $rows;
	public $columns;
	public function __construct($username, $text, $rows, $cols) 
	{
		parent::__construct('editbox', "Editing [$username]'s Plan");
		$this->username = $username;
		$this->rows = $rows;
		$this->columns = $cols;
		$this->text = $text;
	}
	public function toHTML() 
	{
		$txta = new FormItem('textarea', 'plan', '');
		$txta->description = $this->text;
		//$txta->identifier = 'edittextarea';
		$this->appendField($txta);
		$this->appendField(new FormItem('submit', '', 'Change Plan'));
		return parent::toHTML();
	}
}
class PlanContent extends Widget
{
	public $username;
	public $text; // A PlanText object
	public $planname;
	public $lastlogin;
	public $lastupdate;
	public $addform; // form to add this plan to autoread
	public function __construct($username, $planname, $lastlogin, $lastupdate, $text) 
	{
		parent::__construct('plan', "[$username]'s Plan");
		$this->username = $username;
		$this->lastlogin = $lastlogin;
		$this->lastupdate = $lastupdate;
		$this->planname = $planname;
		$this->text = $text;
	}
	public function toHTML() 
	{
		return "Warning: toHTML() unimplemented for this object (PlanContent).";
	}
}
// Don't use this class, use one of its subclasses below
class Text extends Widget
{
	/* a string with the text to be displayed. */
	/* Please note: this string may contain some basic HTML in it,
	* namely <i> and <b> tags, and hyperlinks. */
	public $message;
	function toHTML() 
	{
		return $this->message;
	}
}
class RegularText extends Text
{
	public function __construct($_message, $title) 
	{
		parent::__construct('text', $title);
		$this->message = $_message;
	}
}
class InfoText extends Text
{
	public function __construct($_message, $title) 
	{
		parent::__construct('infomessage', $title);
		$this->message = $_message;
	}
}
class AlertText extends Text
{
	/* boolean, is this alert a critical error (such as a crash)? */
	public $error;
	public function __construct($_message, $_error) 
	{
		parent::__construct('alertmessage', 'Alert');
		$this->message = $_message;
	}
}
class RequestText extends Text
{
	public function __construct($_message) 
	{
		parent::__construct('requestmessage', 'Question');
		$this->message = $_message;
	}
}
class HeadingText extends Text
{
	/* int representing the nesting level of the heading (1 is a top level heading) */
	public $sublevel;
	public function __construct($_message, $_level) 
	{
		parent::__construct('heading' . $sublevel, NULL);
		$this->message = $_message;
		$this->sublevel = $_level;
	}
}
/* For storing the contents of a user's plan. */
//TODO currently this isn't used. Use it or deprecate it
class PlanText extends Text
{
	/* A boolean: true if the text is in plans markup (i.e. [b] for
	* bold text), and false if text is in HTML form. */
	public $planmarkup;
	public function __construct($_message, $_planmarkup) 
	{
		parent::__construct('plantext', "Plan Text");
		$this->message = $_message;
		$this->planmarkup = $_planmarkup;
	}
}
