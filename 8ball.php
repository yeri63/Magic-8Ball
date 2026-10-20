<?php
    # Program: 8ball.php
    #    Date: 18-Oct-2023
    #  Author: gerry.walterscheid@gmail.com
    # Descrip: Turn the IP Phone into a Magic 8 Ball!.

    session_start();
    header("Content-type: text/xml");

    $delay = 5;
    header("Refresh:$delay");

    $urlPath = "http://192.168.3.250/cisco/8ball.php";

    # Used to adjust puzzle width to match current phone model.
    function getCiscoPhoneModel() {
        return $_SERVER['HTTP_X_CISCOIPPHONEMODELNAME'] ?? 'unknown';
    }

    $reply = array( 
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
    $id = $_SESSION['id'] ?? "";            # App name.

    # Prevent data corruption to next application.
    if ($id == "8ball") {
        $lastCmd = $_SESSION['cmd'];        # User command.
        $update = $_SESSION['update'];      # Time from prior page update.
        $state = $_SESSION['state']; }      # Display state.
     else {
        $update = 0;
        $state = 0; }

    # Get SoftKey selection.
    $cmd = $_GET['key'] ?? 0;

    # Check page refresh delay.
    if ((time() - $update) >= $delay)
        $cmd = 0;

    # Process user request based on the key pressed.
    switch($cmd) {

    case 0 : # Clear display.
        $state = 0;
        break;

    case 1 : # Shaking ball.
        $state = 1;
        break;
    }

    echo "<CiscoIPPhoneText>\n";
    echo "  <Title>Magic 8 Ball</Title>\n";

    switch($state) {

    case 0 : # Display blank screen, ie. without a reply.

        echo "  <Text>\n";
        echo "  </Text>\n";
        break;

    case 1 : # Display reply after shaking the ball.

        $i = mt_rand(1,$count) - 1;
        $model = getCiscoPhoneModel();

        # Adjust text position based on phone model.

        switch($model) {
            case 'CP-7911G' :
               $columns = 30;
               $rows = 3;
                break;

            case 'CP-7961G-GE' :
               $columns = 40;
               $rows = 5;
                break;

            default : # Cisco Communicator
               $columns = 40;
               $rows = 8;
               break;
        }

        # Center response on display.

        $length = strlen($reply[$i]);
        $indent = intval(($columns - $length) / 2);

        $spaces = str_repeat("\t", intval($indent / 1.5));
        $lines = str_repeat("\n", intval($rows / 2));
		
        echo "  <Text>\n";
        echo "$lines";
        echo "$spaces$reply[$i]\n\n\n\n";
        echo "  </Text>\n";

        break;
    }

    $mesg = "Ask question, then shake ball.";

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

    echo "  <Prompt>$mesg</Prompt>\n";
    echo "</CiscoIPPhoneText>";

    # Save current variable state.
    $_SESSION['id'] = "8ball";
    $_SESSION['state'] = $state;
    $_SESSION['cmd'] = $cmd;
    $_SESSION['update'] = time();
?>

