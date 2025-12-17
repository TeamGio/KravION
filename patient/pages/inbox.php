<div class="card">
    <h3><?php echo $t['my inbox']; ?></h3>


    <?php
        // Språkhantering för blogg (om du vill ha det)
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
        <p style="color: #6C757D; margin-top: 30px;"><?php echo $t['no_messages']; ?></p>
    <?php endif; ?>
</div>




<?php
// Hämta journalanteckningar encounter
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);

$show_form = false;
$today_date = date('Y-m-d'); 

// Loopa igenom journalerna för att se om vi ska visa formuläret
foreach ($encounters as $enc) {
    if (isset($enc['status']) && 
        $enc['status'] === 'Completed' && 
        $enc['encounter_date'] === $today_date) {
            
        $show_form = true;
        break; 
    }
}
?>



<div class="card" style="margin-top: 20px;">
    <h2><?php echo $t['feedback_header']; ?></h2>

    <form method="POST"><br><br>

        <label><?php echo $t['age_label']; ?>:</label><br>
        <select name="age" required>
            <option value="" disabled selected><?php echo $t['choose_age']; ?></option>
            <option value="18-25">18-25</option>
            <option value="26-35">26-35</option>
            <option value="36-50">36-50</option>
            <option value="51-65">51-65</option>
            <option value="65+">65+</option>
        </select>
        <br><br>

        <label><?php echo $t['gender_label']; ?>:</label><br>
        <select name="gender" required>
            <option value="" disabled selected><?php echo $t['choose_gender']; ?></option>
            <option value="Man"><?php echo $t['gender_man']; ?></option>
            <option value="Kvinna"><?php echo $t['gender_woman']; ?></option>
            <option value="Annat"><?php echo $t['gender_other']; ?></option>
            <option value="Vill ej uppge"><?php echo $t['gender_no_say']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_opportunity']; ?></label><br>
        <select name="questions_opportunity" required>
            <option value="" disabled selected><?php echo $t['choose_gender']; // (Återanvänder "Välj..." texten) ?></option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
            <option value="Delvis"><?php echo $t['partially']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_info_clarity']; ?></label><br>
        <select name="info_clarity" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
            <option value="Delvis"><?php echo $t['partially']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_contact']; ?></label><br>
        <select name="contact_satisfaction" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_visit_time']; ?></label><br>
        <select name="visit_time_reasonable" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_wait_room']; ?></label><br>
        <select name="waiting_room_time" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_treatment_info']; ?></label><br>
        <select name="treatment_info" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
            <option value="Delvis"><?php echo $t['partially']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_staff_response']; ?></label><br>
        <select name="staff_response" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
            <option value="Delvis"><?php echo $t['partially']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['q_staff_explain']; ?></label><br>
        <select name="staff_explanation" required>
            <option value="" disabled selected>Välj...</option>
            <option value="Ja"><?php echo $t['yes']; ?></option>
            <option value="Nej"><?php echo $t['no']; ?></option>
            <option value="Delvis"><?php echo $t['partially']; ?></option>
        </select>
        <br><br>

        <label><?php echo $t['extra_comments']; ?></label><br>
        <textarea name="extra_comments" rows="4" cols="50"></textarea>
        <br><br>

        <button type="submit"><?php echo $t['submit_feedback']; ?></button>

    </form>
</div>


<?php
// === BLOGGPOST ===
$blog_url = ($lang === 'en')
    ? 'http://193.93.250.83:8080/blog/opening%20hours/blogpost'
    : 'http://193.93.250.83:8080/blog/%C3%B6ppettider/bloggpost';
?>

<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['opening_hours_header']; ?></h3>

    <iframe 
        src="<?php echo $blog_url; ?>"
        style="border: none; width: 100%; height: 700px;">
    </iframe>
</div>