<?php
/**
*
* @package Interfaces
*
* Object-Oriented stuff for Plans!
* Defines a set of objects that are constructed by pages and then passed
* to the interface, where they are turned into HTML or whatever else.
*
* Note: Some of the objects implement a toHTML() method.  This is for
* convenience only, and interfaces are by no means required to make use
* of these methods.
*/

/**
 * This is an entire page of content generated by plans. Passed whole to the
 * interface for printing.
 * @todo Should all these vars be public?
 */
class PlansPage
{
	/**
	 * What category this page belongs to
	 * @var string
	 */
	public $group;
	/**
	 * A unique identifier for this page
	 * @var string
	 */
	public $identifier = 'defaultpage';
	/**
	 * A title for the page
	 * @var string
	 */
	public $title = PLANSVNAME;
	/**
	 * The main panel that contains all the links, autoreads, etc
	 * @var MainPanel
	 */
	public $mainpanel = NULL;
	/**
	 * An array of Widgets, the contents of this page
	 * @var array
	 */
	public $contents = array();
	/**
	 * The footer of the page
	 * @var Footer
	 */
	public $footer;
	/**
	 * The stylesheet to use - a URL
	 * @var string
	 */
	public $stylesheet;
	/**
	 * @todo needed?
	 */
	public $searchname;
	/**
	 * The URL of this page
	 * @var string
	 */
	public $url;
	/**
	 * The current priority level of the autoread list
	 * @var int
	 */
	public $autoreadpriority;

	/**
	 * @param string $group the group this page belongs to
	 * @param int $id a unique identifier for this page
	 * @param string a title for the page
	 * @param string the URL of this page
	 */
	function __construct($group, $id, $title, $url) 
	{
		$this->group = $group;
		$this->identifier = $id;
		$this->title = $title;
		$this->url = $url;
	}

	/**
	 * Add a widget to the page.
	 * @param Widget &$widget the widget to be added.  Uses by reference, so widget
	 * may still be modified after being passed to this method.
	 */
	public function append(Widget &$widget) 
	{
		$this->contents[] = $widget;
	}
}
/**
 * The main panel of links for plans. Includes the finger box, links to different
 * parts of Plans, and the autoread lists.
 */
class MainPanel
{
	public $identifier = 'mainpanel';
	/**
	 * Link back to the home page
	 * @var Hyperlink;
	 */
	public $linkhome;
	/**
	 * Gives the Finger (or Read) box
	 * @var Form
	 */
	public $fingerbox;
	/**
	 * An array of the basic links
	 * $var array
	 */
	public $requiredlinks;
	/**
	 * An array of optional links
	 * @var array
	 */
	public $optionallinks;
	/**
	 * An array of AutoRead objects
	 * @var array
	 */
	public $autoreads;
}

/**
 * The footer found on most pages.  Currently may contain the
 * "do you read?" thing and legalese stuff.
 */
class Footer
{
	/**
	 * A link to a recently updated plan
	 * @var PlanLink
	 */
	public $doyouread;
	/**
	 * Whatever legalese text goes at the bottom
	 * @var InfoText
	 * @todo currently InfoText, does it need its own class?
	 */
	public $legal;
	
}
/**
 * General-purpose class for things found within a PlansPage.
 * Should not actually be used, just exists to be extended by other classes.
 */
class Widget
{
	/**
	 * A unique identifier for this item
	 * @var null|string
	 */
	public $identifier;
	/**
	 * A group name of logically related widgets
	 * @var null|string
	 */
	public $group;
	/**
	 * Optional - if we desire additional attributes in the tag when we
	 * convert to HTML, this field serves as a convenience. <b>Should only
	 * be set by the interface.</b>
	 * @var string
	 */
	public $html_attributes;

	/**
	 * @param string $identifier An identifier for this widget. Should give a meaningful
	 * name for the purpose of this widget.
	 * @param boolean $unique is $identifier unique? In other words, will this widget
	 * be the only one with the given identifier on a given page?
	 *
	 * <b>Note:</b> While this identification scheme, incidentally, is quite similar to
	 * the id and class properties in HTML, don't assume that this is the only way they
	 * may be used.
	 */
	function __construct($identifier, $unique) 
	{
		if ($unique) {
			$this->identifier = $identifier;
		} else {
			$this->group = $identifier;
		}
	}

	/**
	 * A convenience function.
	 *
	 * Returns a string of simple HTML code that represents this widget.  Defined by
	 * child classes. Do not assume that all interfaces will make use of this method.
	 * All widgets must provide enough information to represent themselves without
	 * the use of this method.
	 *
	 * @return string the HTML result
	 */
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
	function __construct($identifier, $unique) 
	{
		parent::__construct($identifier, $unique);
		$this->contents = array();
	}
	public function append(Widget $widge) 
	{
		$this->contents[] = $widge;
	}
	public function toHTML($callback = null) 
	{
		foreach($this->contents as $item) {
			if ($callback == null) {
				$str = $str . "\n" . $item->toHTML();
			} else {
				// Invoke the callback
				$str.= call_user_func($callback, $item);
			}
		}
		return $str;
	}
}
/**
 * Similar to WidgetGroup, but implies more of a structural relationship between elements.
 */
class WidgetList extends WidgetGroup
{
	/**
	 * A title for this list
	 * @var null|string
	 */
	public $title;

	function __construct($identifier, $unique, $title=null) 
	{
		parent::__construct($identifier, $unique);
		$this->title = $title;
	}
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
		parent::__construct("autoreadlev$p", true, "Level $p");
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
	function __construct($id, $unique, $href, $desc) 
	{
		parent::__construct($id, $unique);
		$this->href = $href;
		$this->description = $desc;
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
	function __construct($username) 
	{
		$href = "read.php?searchname=$username";
		parent::__construct('planlove', false, $href, $username);
		
	}
}
/**
 * A web form.
 */
class Form extends WidgetGroup
{
    /* These constants attempt to formalize what content we are expecting.
     * This information could be used for client-side sanity checking with
     * JavaScript, or to transparently reimplement a form in XForms.
     */
	const FIELD_NUMERIC = 10;
	const FIELD_TEXT = 15;

        /* basic settings for the form */
	public $action;
	public $method;

        /* an array containing the fields in the form, as FormItems */
        public $contents;

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
	/* Rows for a textarea */
	public $rows;
	/* Cols for a textarea */
	public $cols;

	public function __construct($type, $name, $value = null) 
	{
		parent::__construct($name, true);
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
		if ($type == 'textarea') {
			// set some defaults
			$this->rows = 3;
			$this->cols = 40;
		}
	}
	public function toHTML() 
	{
		$str = '';
		switch ($this->type) {
			case 'widget':
				$str = $this->value->toHTML();
				break;

			case 'textarea':
				$str = $str . "<textarea name=\"$this->name\" rows=\"$this->rows\" cols=\"$this->cols\">" . $this->description . '</textarea>';
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
		parent::__construct('editbox', true);
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
		parent::__construct('plan', true);
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
        /**
         * A string used to preface some text widgets. Details of
         * implementation left up to the interface.
         */
        public $title;

	/* a string with the text to be displayed. */
	/* Please note: this string may contain some basic HTML in it,
	* namely <i> and <b> tags, and hyperlinks. */
	public $message;

	public function __construct($group, $title) {
		parent::__construct($group, false);
		$this->title = $title;
	}

	function toHTML() 
	{
		return $this->message;
	}
}
class RegularText extends Text
{
	public function __construct($message, $title) 
	{
		parent::__construct('text', $title);
		$this->message = $message;
	}
}
class InfoText extends Text
{
	public function __construct($_message, $title='') 
	{
		parent::__construct('infomessage', $title);
		$this->message = $_message;
	}
}
class AlertText extends Text
{
	/* boolean, is this alert a critical error (such as a crash)? */
	public $error;
	public function __construct($message, $title, $error=false) 
	{
		parent::__construct('alertmessage', $title);
		$this->message = $message;
		$this->error = $error;
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
class Secret extends Text
{
	public $date;
	public $secret_id;

	public function __construct($text) 
	{
		parent::__construct('secret', '');
		$this->message = $text;
	}
}
/* For storing the contents of a user's plan. */
class PlanText extends Text
{
	/* A boolean: true if the text is in plans markup (i.e. [b] for
	 * bold text), and false if text is in HTML form. 
	 * @todo currently unused, I think */
	public $planmarkup;
	public function __construct($_message, $_planmarkup) 
	{
		parent::__construct('plantext', "Plan Text");
		$this->message = $_message;
		$this->planmarkup = $_planmarkup;
	}
}
