<?php
function efRunEmailMe( $par ) {
    global $wgEmailMeAddress;
    $sp = new EmailMe;
    $sp->run( $par );
    return true;
}
 
class EmailMe extends SpecialPage {
    function EmailMe() {
        global $wgEmailMeAddress;
        SpecialPage::SpecialPage("EmailMe", '', true, 'efRunEmailMe');
        wfLoadExtensionMessages('EmailMe');
        $this->mAddress = $wgEmailMeAddress;
        return true;
    }

    function run( $par ) {
        global $wgRequest, $wgEmailMeAddress, $wgOut;

        if ($wgRequest->wasPosted ()) {
            $from = $wgRequest->getText ('from');
            $subject = $wgRequest->getText ('subject');
            $message = $wgRequest->getText ('message');
            if(! ($from && $subject && $message) )
                return $this->print_form ($this->getTitle (), $subject, $from, $message,wfMsg('emailme-incomplete'));
	    if(! User::isValidEmailAddr( $from ) )
		return $this->print_form ($this->getTitle (), $subject, $from, $message, wfMsg('emailme-invalid-email'));

            $mailResult = UserMailer::send( new MailAddress($wgEmailMeAddress),new MailAddress($from), $subject, $message);

            if( WikiError::isError( $mailResult ) ) {
                return $this->print_form ($this->getTitle (), $subject, $from, $message, 'Sorry: '.$mailResult->toString ());
                
		dvLog( "ERROR: EmailMe::run(" . $mailResult->toString() . ") $from|$subject");
            } else {
		
		dvLog( "EMAIL: $from -> $wgEmailMeAddress ( $subject ) ");
                return $this->print_success ();
            }
        }

        return $this->print_form ($this->getTitle (), str_replace('_',' ',$par));
    }

    function print_success () {
        global $wgOut;
	$wgOut->addWikiText( wfMsg('emailme-success'));
        return true;
    }
    function print_form( $post_title, $subject='', $from='', $message='', $errormessage='' ) {
        global $wgOut;
        
        $from = htmlspecialchars ($from);
        $subject = htmlspecialchars ($subject);
        $message = htmlspecialchars ($message);
        $post_url = htmlspecialchars ($post_title->getLocalURL ());

	$emaillabel = wfMsg('emailme-email');
	$subjectlabel = wfMsg('emailme-subject');
	$assurance = wfMsg('emailme-assurance');
	$send = wfMsg('emailme-send');

        $wgOut->addHTML( <<<qaz
<form method="post" action="$post_url" class="emailme">
 <table style="background: inherit;">
  </tr>
   <th style="text-align: right"><label for="from">$emaillabel</label></th>
   <td><input type="text" name="from" size="50" value="$from" /></td>
   <td style="font-size: 0.8em">$assurance</td>
  </tr><tr>
   <th style="text-align: right"><label for="subject">$subjectlabel</label></th>
   <td colspan="2"><input type="text" name="subject" size="80" value="$subject"/></td>
  </tr><tr>
   <td colspan="3">
    <textarea name="message" rows="10">$message</textarea>
   </td>
  </tr><tr>
   <td><input type="submit" name="send" value="$send"/></td>
   <td style="color: #F00;">$errormessage</td>
  </tr>
 </table>
</form>
qaz
);
        return true;
    }
}

?>
