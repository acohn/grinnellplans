<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect(); //establish the database handler
$idcookie = User::id();
$thispage = new PlansPage('Notes', 'board_messages', PLANSVNAME . ' - Notes', 'board_messages.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to use Notes as a guest.', 'Access Denied');
    $thispage->append($denied);
} else
//elseallowed to edit
{
    populate_page($thispage, $dbh, $idcookie);
    $content = new WidgetGroup('notes_content', false);
    $thispage->append($content);
    $header = new WidgetGroup('notes_header', false);
    $content->append($header);
    $replyform = new Form('notes_replyform', true);
    $replyform->action = 'board_submit.php';
    $replyform->method = 'post';
    $replylink = new DisplayToggleLink('post_reply_toggle', true, $replyform, 'Reply', 'Cancel');
    $content->append($replylink);
    $content->append($replyform);
    $cook = new HiddenInput('checknum', $idcookie);
    $replyform->append($cook);
    $thread = new HiddenInput('threadid', $_REQUEST['threadid']);
    $replyform->append($thread);
    $sub = new HiddenInput('submit', 1);
    $replyform->append($sub);
    $text = new TextareaInput('messagecontents');
    $text->rows = 11;
    $text->cols = 50;
    $text->title = 'Post Reply';
    $replyform->append($text);
    $button = new SubmitInput('Submit');
    $replyform->append($button);
    $messagenum = (isset($_GET['messagenum']) ? $_GET['messagenum'] : 0);
    $threadid = (isset($_GET['threadid']) ? $_GET['threadid'] : 0);
    if ($messagenum > 0) {
        $messagevals = get_items($dbh, "threadid, created", "subboard", "messageid", $messagenum);
        $threadid = $messagevals[0][0];
        if (!$threadid) {
            $error_message = new AlertText("The message you requested has been deleted or does not exist.", 'Error');
            $thispage->append($error_message);
            interface_disp_page($thispage);
            stop();
        }
        $my_result = mysql_query("Select COUNT(*) From subboard WHERE userid != 0 AND created >= \"" . $messagevals[0][1] . "\" and threadid=\"" . $threadid . "\"");
        $pagefind = mysql_fetch_row($my_result);
        $pagenumber = floor($pagefind[0] / NOTES_MSGS_PER_PAGE);
    }
    $my_result = mysql_query("Select COUNT(*) From subboard WHERE userid != 0 AND threadid=\"" . $threadid . "\"");
    $totalmessages = mysql_fetch_row($my_result);
    $pagenumber = (isset($_GET['pagenumber']) ? $_GET['pagenumber'] : 0);
    if (!($pagenumber > 0)) {
        $pagenumber = 0;
    }
    if ($pagenumber > floor($totalmessages[0] / NOTES_MSGS_PER_PAGE)) {
        $pagenumber = floor($totalmessages[0] / NOTES_MSGS_PER_PAGE);
    }
    $nav = new NotesNavigation('board_nav', false);
    $header->append($nav);
    if ($pagenumber > 0) {
        $nav->newest = new Hyperlink('newest', false, 'board_messages.php?pagenumber=0&threadid=' . $threadid, '&lt;&lt;');
    }
    if ($pagenumber >= 2) {
        $tempnum = $pagenumber - 2;
        $nav->even_newer = new Hyperlink('newer', false, 'board_show.php?pagenumber=' . $tempnum . "&threadid=" . $threadid, $tempnum);
    }
    if ($pagenumber >= 1) {
        $tempnum = $pagenumber - 1;
        $nav->newer = new Hyperlink('newer', false, 'board_messages.php?pagenumber=' . $tempnum . "&threadid=" . $threadid, $tempnum);
    }
    $nav->current = new RegularText($pagenumber);
    if ($totalmessages[0] > ($pagenumber + 1) * NOTES_MSGS_PER_PAGE) {
        $tempnum = $pagenumber + 1;
        $nav->older = new Hyperlink('older', false, 'board_messages.php?pagenumber=' . $tempnum . "&threadid=" . $threadid, $tempnum);
    }
    if ($totalmessages[0] > ($pagenumber + 2) * NOTES_MSGS_PER_PAGE) {
        $tempnum = $pagenumber + 2;
        $nav->even_older = new Hyperlink('oldest', false, 'board_messages.php?pagenumber=' . $tempnum . "&threadid=" . $threadid, $tempnum);
    }
    if (floor($totalmessages[0] / NOTES_MSGS_PER_PAGE) > $pagenumber) {
        $nav->oldest = new Hyperlink('oldest', false, 'board_messages.php?pagenumber=' . floor($totalmessages[0] / NOTES_MSGS_PER_PAGE) . '&threadid=' . $threadid, '&gt;&gt;');
    }
    $rowoffset = NOTES_MSGS_PER_PAGE * $pagenumber;
    $thread = new NotesTopic('notes_thread', true);
    $content->append($thread);
    $this_title = stripslashes(get_item($dbh, "title", "mainboard", "threadid", $threadid));
    $thread->title = new HeadingText($this_title, 3);
    $thread->title->identifier = 'topic_title';
    $notes_pref = get_item($dbh, "notes_asc", "accounts", "userid", $userid);
    if ($notes_pref) {
        $rowoffset = $totalmessages[0] - NOTES_MSGS_PER_PAGE * ($pagenumber + 1);
        if ($rowoffset < 0) {
            $rowoffset = 0;
        }
    }
    $query = "Select subboard.messageid, 
                UNIX_TIMESTAMP(subboard.created),
                subboard.userid, accounts.username, subboard.title ,subboard.contents, ifnull(vts.votes,0), mv.vote, ifnull(vts.num_votes,0)
                From 
                subboard left join  accounts using (userid)
                left join (select messageid, sum(vote) as votes, count(*) as num_votes from boardvotes 
                           where threadid = " . $threadid . "
                           group by messageid) as vts on 
                vts.messageid = subboard.messageid
                left join (select messageid, vote from boardvotes where userid = " . $idcookie . ") as mv
                     on mv.messageid = subboard.messageid
                where subboard.threadid = " . $threadid . "
		AND userid != 0 
                ORDER BY subboard.messageid DESC 
                LIMIT " . $rowoffset . "," . NOTES_MSGS_PER_PAGE;
    $my_result = mysql_query($query);
    $colorswitch = 0;
    while ($new_row = mysql_fetch_row($my_result)) {
        $post = new NotesPost('notes_post', false);
        $thread->append($post);
        if ($new_row[3]) {
            $post->poster = new PlanLink($new_row[3]);
        }
        if ($new_row[7] == 1) {
            $post->user_vote = 'yes';
        }
        if ($new_row[7] == - 1) {
            $post->user_vote = 'no';
        }
        $post->id = $new_row[0];
        $post->score = $new_row[6];
        $post->votes = $new_row[8];
        $post->date = $new_row[1];
        $post->contents = stripslashes($new_row[5]);
    }
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
