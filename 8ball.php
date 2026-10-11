<?php
    # Program: 8ball.php
    #    Date: 18-Oct-2023
    #  Author: gerry.walterscheid@gmail.com
    # Descrip: Turn the IP Phone into a Magic 8 Ball!.

    session_start();
    header("Content-type: text/xml");

    $delay = 60;
    header("Refresh:$delay");

    $urlPath = "http://192.168.3.250/cisco/8ball.php";

    # Used to adjust puzzle width to match current phone model.
    function getCiscoPhoneModel() {
        return $_SERVER['HTTP_X_CISCOIPPHONEMODELNAME'] ?? 'unknown';
    }

    $msg = array( 
        "It is certain.", "It is decidedly so.",                # Positive 
        "Without a douobt.", "Yes - definitely.",               # Poitives
        "You may rely on it.", "As I see it, yes.",             # Positive 
        "Most likely.", "Outlook good.",                        # Positive 
        "Yes.", "Signs point to yes.",                          # Positive 
        "Reply hazy, try agian.", "Ask again later.",           # Neutral 
        "Better not tell you now.", "Cannot predict now.",      # Neutral 
        "Concentrate and ask again.",                           # Neutral
        "Don't count on it.", "My reply is no.",                # Negative
        "My sources say no.", "Outlook not so good.",           # Negative
        "Very doubtful." );                                     # Negative

    $count = 20;

    # Recall prior variable state
    $id = $_SESSION['id'] ?? "";                  # App name.

    # Prevent data corruption to next application.
    if ($id == "8ball") {
        $lastCmd = $_SESSION['cmd'];        # User command.
        $update = $_SESSION['update'];      # Time from prior page update.
        $mode = $_SESSION['mode']; }        # Display mode.
     else {
        $update = 0;
        $mode = 0; }

    # Get SoftKey selection.
    $cmd = $_GET['key'] ?? 0;

    # Check page refresh delay.
    if ((time() - $update) >= $delay)
        $cmd = 0;
        
    # Process user request based on the key pressed.    
    switch($cmd) {

    case 0 : # Clear display.
        $mode = 0;
        break;

    case 1 : # Display reply.
        $mode = 1;
        break;
    }

    echo "<CiscoIPPhoneText>\n";
    echo "  <Title>Magic 8 Ball</Title>\n";

    switch($mode) {

    case 0 : # Display blank screen, ie. without reply.
        echo "  <Text>\n\n";
        echo "  </Text>\n";
        break;

    case 1 : # Display reply after shaking the ball.

        $i = mt_rand(1,$count) - 1;
        $model = getCiscoPhoneModel();

        # Adjust text position based on phone model.
        if ($model == "CP-7911G") {
            $line = 28;
            $factor = 1.8; }
        else {
            $line = 30;
            $factor = 1.6;
            echo "\n"; }

        # Center response on display.
        $length = intval(strlen($msg[$i]) / $factor);
        $indent = intval((($line - $length) / 2) * $factor);
        $spaces = str_repeat(" ", $indent);

        echo "  <Text>\n\n";
        echo "$spaces$msg[$i]\n";
        echo "  </Text>\n";
        break;
    }

    echo "  <SoftKeyItem>\n";
    echo "    <Name>Shake</Name>\n";
    echo "    <URL>$urlPath?key=1</URL>\n";
    echo "    <Position>1</Position>\n";
    echo "  </SoftKeyItem>\n";

    echo "  <SoftKeyItem>\n";
    echo "    <Name>Exit</Name>\n";
    echo "    <URL>SoftKey:Exit</URL>\n";
    echo "    <Position>3</Position>\n";
    echo "  </SoftKeyItem>\n";

    echo "  <Prompt>Ask question, then shake ball.</Prompt>\n";
    echo "</CiscoIPPhoneText>";

    # Save current variable state.
    $_SESSION['id'] = "8ball";
    $_SESSION['mode'] = $mode;
    $_SESSION['update'] = time();
?>
