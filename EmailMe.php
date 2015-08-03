<?php
 
$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['EmailMe'] = $dir . 'EmailMe_body.php'; # Tell MediaWiki to load the extension body.
$wgExtensionMessagesFiles['EmailMe'] = $dir . 'EmailMe.i18n.php';
$wgSpecialPages['EmailMe'] = 'EmailMe'; # Let MediaWiki know about your new special page.
$wgHooks['LanguageGetSpecialPageAliases'][] = 'emailMeLocalizedPageName'; # Add any aliases for the special page.
 
function emailMeLocalizedPageName(&$specialPageArray, $code) {
  # The localized title of the special page is among the messages of the extension:
  wfLoadExtensionMessages('EmailMe');
  $text = wfMsg('emailme');
 
  # Convert from title in text form to DBKey and put it into the alias array:
  $title = Title::newFromText($text);
  if ($title)
    $specialPageArray['EmailMe'][] = $title->getDBKey();
  else
	$specialPageArray['EmailMe'][] =  $text;
 
  return true;
}


$wgExtensionCredits['other'][] = array(
    'name'        => 'Email Me',
    'author'      => 'Conrad Irwin',
    'description' => 'Creates a friendly Special page where anons can Email_<name>',
);

?>
