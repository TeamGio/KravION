<div class="card">
    <h3><?php echo $t['my inbox']; ?></h3>


    <?php
        $new_lang = ($lang === 'sv') ? 'en' : 'sv';


        $messages = $erp_client->getMessagesForPatient($patient_erp_id);
        if (count($messages) > 0):
    ?>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left;">
                    <th style="padding: 10px; width: 33%;">  <?php echo $t['practitioner']; ?></th>
                    <th style="padding: 10px; width: 33%;">  <?php echo $t['subject']; ?></th>
                    <th style="padding: 10px; width: 33%;">  <?php echo $t['date']; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 10px;">
                            <strong><?php echo htmlspecialchars($msg['prac_name'] ?? 'Inget namn'); ?></strong>
                        </td>


                        <td style="padding: 10px;">
                            <?php echo htmlspecialchars($msg['subject'] ?? 'Inget ämne'); ?>
                        </td>


                        <td style="padding: 10px;">
                            <?php
                            $date = strtotime($msg['creation']);
                            echo date('Y-m-d H:i', $date);
                            ?>
                        </td>

                    </tr>


                    <tr>
                        <td colspan="4" style="border: none; padding: 0;">
                            <details>
                                <summary><?php echo $t['read message']; ?></summary>
                                <p>
                                    <?php echo $msg['message'] ?? 'Inget innehåll.'; ?>
                                </p>
                            </details>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    <?php else: ?>
        <p style="color: #6C757D; margin-top: 30px;">Du har inga meddelanden i inkorgen.</p>
    <?php endif; ?>
</div>




<?php
// Hämta journalanteckningar encounter
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);

$show_form = false;
$today_date = date('Y-m-d'); 

// Loopa igenom journalerna
foreach ($encounters as $enc) {
    if (isset($enc['status']) && 
        $enc['status'] === 'Completed' && 
        $enc['encounter_date'] === $today_date) {
            
        $show_form = true;
        break;  // Avsluta loopen om hittat en match
    }
}
?>



<div class="card" style="margin-top: 20px;">
    <h2>Blev du nöjd med ditt senaste besök?</h2>

    <form method="POST"><br><br>

        <label>Ålder:</label><br>
        <select name="age" required>
            <option value="" disabled selected>Välj ålder...</option>
            <option value="18-25">18-25</option>
            <option value="26-35">26-35</option>
            <option value="36-50">36-50</option>
            <option value="51-65">51-65</option>
            <option value="65+">65+</option>
        </select>
        <br><br>

        <label>Kön:</label><br>
        <select name="gender" required>
            <option value="" disabled selected>Välj kön...</option>
            <option value="Man">Man</option>
            <option value="Kvinna">Kvinna</option>
            <option value="Annat">Annat</option>
            <option value="Vill ej uppge">Vill ej uppge</option>
        </select>
        <br><br>

        <label>Fick du möjlighet att ställa frågorna du önskade?</label><br>
        <select name="questions_opportunity" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
            <option value="Delvis">Delvis</option>
        </select>
        <br><br>

        <label>Var det enkelt att ta till sig informationen?</label><br>
        <select name="info_clarity" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
            <option value="Delvis">Delvis</option>
        </select>
        <br><br>

        <label>Är du nöjd med hur du kan kontakta oss?</label><br>
        <select name="contact_satisfaction" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
        </select>
        <br><br>

        <label>Fick du besöka vårdcentralen inom rimlig tid?</label><br>
        <select name="visit_time_reasonable" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
        </select>
        <br><br>

        <label>Var väntan i väntrummet längre än 20 min?</label><br>
        <select name="waiting_room_time" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
        </select>
        <br><br>

        <label>Fick du tillräcklig info om behandling/bieffekter?</label><br>
        <select name="treatment_info" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
            <option value="Delvis">Delvis</option>
        </select>
        <br><br>

        <label>Fick du svar från personalen som du förstod?</label><br>
        <select name="staff_response" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
            <option value="Delvis">Delvis</option>
        </select>
        <br><br>

        <label>Förklarade personalen behandlingen på ett bra sätt?</label><br>
        <select name="staff_explanation" required>
            <option value="" disabled selected>Välj alternativ...</option>
            <option value="Ja">Ja</option>
            <option value="Nej">Nej</option>
            <option value="Delvis">Delvis</option>
        </select>
        <br><br>

        <label>Övriga kommentarer:</label><br>
        <textarea name="extra_comments" rows="4" cols="50"></textarea>
        <br><br>

        <button type="submit">Skicka in svar</button>

    </form>
</div>








<?php
// === BLOGGPOST – SAMMA PRINCIP SOM APPOINTMENTS ===
$blog_url = ($lang === 'en')
    ? 'http://193.93.250.83:8080/blog/opening%20hours/blogpost'
    : 'http://193.93.250.83:8080/blog/%C3%B6ppettider/bloggpost';
?>

<div class="card" style="margin-top: 20px;">
    <h3>
        <?php echo ($lang === 'en') 
            ? 'Information & Opening Hours' 
            : 'Information & Öppettider'; 
        ?>
    </h3>

    <iframe 
        src="<?php echo $blog_url; ?>"
        style="border: none; width: 100%; height: 700px;">
    </iframe>
</div>