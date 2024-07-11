<?php
// Initialize the session
session_start();

// Function to calculate combined votes for parties of the same leaning
function calculate_combined_votes($party_data) {
    // Combine votes for parties of the same leaning
    $combined_votes = [
        'LNP' => $party_data['LNP'] + $party_data['ONP'],
        'ALP' => $party_data['ALP'] + $party_data['GRN'],
        'IND' => $party_data['IND'],
        'OTHER' => $party_data['OTHER']
    ];

    return $combined_votes;
}

// Initialize variables for combined votes
$combined_votes = [
    'LNP' => 0,
    'ALP' => 0,
    'IND' => 0,
    'OTHER' => 0
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['electorate'])) {
    // Get the selected electorate
    $selected_electorate = $_POST['electorate'];

    // Get the percentage of votes so far for each party
    $lnp = isset($_POST['lnp_percent']) ? floatval($_POST['lnp_percent']) : 0;
    $alp = isset($_POST['alp_percent']) ? floatval($_POST['alp_percent']) : 0;
    $grn = isset($_POST['grn_percent']) ? floatval($_POST['grn_percent']) : 0;
    $onp = isset($_POST['onp_percent']) ? floatval($_POST['onp_percent']) : 0;
    $ind = isset($_POST['ind_percent']) ? floatval($_POST['ind_percent']) : 0;
    $other = isset($_POST['other_percent']) ? floatval($_POST['other_percent']) : 0;

    // Save data to session for the selected electorate
    $_SESSION['electorate_data'][$selected_electorate] = [
        'lnp' => $lnp,
        'alp' => $alp,
        'grn' => $grn,
        'onp' => $onp,
        'ind' => $ind,
        'other' => $other
    ];

    // Prepare data for combined votes calculation
    $party_data = [
        'LNP' => $lnp,
        'ALP' => $alp,
        'GRN' => $grn,
        'ONP' => $onp,
        'IND' => $ind,
        'OTHER' => $other
    ];

    // Calculate combined votes
    $combined_votes = calculate_combined_votes($party_data);
}

// Retrieve data for the selected electorate
$selected_electorate_data = isset($_POST['electorate']) && isset($_SESSION['electorate_data'][$_POST['electorate']]) 
    ? $_SESSION['electorate_data'][$_POST['electorate']] 
    : ['lnp' => 0, 'alp' => 0, 'grn' => 0, 'onp' => 0, 'ind' => 0, 'other' => 0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calculator - 6 News Elections</title>
</head>
<body>
    <h1>6 News Election Calculator</h1>
    
    <form method="post">
        <label for="electorate">Select Electorate:</label>
        <select id="electorate" name="electorate" onchange="this.form.submit()">
            <?php if (isset($_SESSION['electorates'])): ?>
                <?php foreach ($_SESSION['electorates'] as $electorate): ?>
                    <option value="<?php echo htmlspecialchars($electorate); ?>" <?php echo isset($_POST['electorate']) && $_POST['electorate'] == $electorate ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($electorate); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </form>

    <h2>Enter Election Data</h2>

    <form method="post">
        <input type="hidden" name="electorate" value="<?php echo isset($_POST['electorate']) ? htmlspecialchars($_POST['electorate']) : ''; ?>">
        <label for="votes_counted">Percentage of Votes Counted:</label>
        <input type="number" id="votes_counted" name="votes_counted" step="0.01" min="0" max="100" value="<?php echo isset($_POST['votes_counted']) ? htmlspecialchars($_POST['votes_counted']) : ''; ?>"> %

        <h3>Party Data</h3>
        <table border="1">
            <tr>
                <th>Party</th>
                <th>% So Far</th>
                <th>Estimated %</th>
            </tr>
            <tr>
                <td style="background-color: #1c3c9d; color: white;">LNP</td>
                <td><input type="number" name="lnp_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($selected_electorate_data['lnp']); ?>"></td>
                <td><input type="text" name="lnp_estimated" readonly></td>
            </tr>
            <tr>
                <td style="background-color: #E13940; color: white;">ALP</td>
                <td><input type="number" name="alp_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($selected_electorate_data['alp']); ?>"></td>
                <td><input type="text" name="alp_estimated" readonly></td>
            </tr>
            <tr>
                <td style="background-color: #10c25b; color: white;">GRN</td>
                <td><input type="number" name="grn_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($selected_electorate_data['grn']); ?>"></td>
                <td><input type="text" name="grn_estimated" readonly></td>
            </tr>
            <tr>
                <td style="background-color: #ff8300; color: white;">ONP</td>
                <td><input type="number" name="onp_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($selected_electorate_data['onp']); ?>"></td>
                <td><input type="text" name="onp_estimated" readonly></td>
            </tr>
            <tr>
                <td style="background-color: #808080; color: white;">IND</td>
                <td><input type="number" name="ind_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($selected_electorate_data['ind']); ?>"></td>
                <td><input type="text" name="ind_estimated" readonly></td>
            </tr>
            <tr>
                <td style="background-color: #d3d3d3; color: black;">OTHER</td>
                <td><input type="number" name="other_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($selected_electorate_data['other']); ?>"></td>
                <td><input type="text" name="other_estimated" readonly></td>
            </tr>
        </table>

        <button type="submit">Calculate</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['electorate'])): ?>
        <?php
        // Determine the party with higher votes
        $party_gain = '';
        $higher_party = '';
        $lower_party = '';

        if ($combined_votes['LNP'] > $combined_votes['ALP']) {
            $higher_party = 'LNP';
            $lower_party = 'ALP';
        } else {
            $higher_party = 'ALP';
            $lower_party = 'LNP';
        }

        if ($combined_votes['GRN'] > $combined_votes['ONP']) {
            $higher_party = 'GRN';
            $lower_party = 'ONP';
        } else {
            $higher_party = 'ONP';
            $lower_party = 'GRN';
        }

        // Calculate estimated gain/hold by party
        $estimated_gain = $combined_votes[$higher_party] - $combined_votes[$lower_party];
        $party_gain = $higher_party;

        // Print estimated gain/hold by party in appropriate color
        $party_color = '';
        switch ($party_gain) {
            case 'LNP':
                $party_color = '#1c3c9d; color: white;';
                break;
            case 'ALP':
                $party_color = '#E13940; color: white;';
                break;
            case 'GRN':
                $party_color = '#10c25b; color: white;';
                break;
            case 'ONP':
                $party_color = '#ff8300; color: white;';
                break;
            case 'IND':
                $party_color = '#808080; color: white;';
                break;
            default:
                $party_color = '#d3d3d3; color: black;';
                break;
        }
        ?>

        <h2 style="background-color: <?php echo $party_color; ?>">Estimated Gain/Hold By <?php echo htmlspecialchars($party_gain); ?></h2>
    <?php endif; ?>

    <p><a href="index.php">Back to Homepage</a></p>
</body>
</html>
