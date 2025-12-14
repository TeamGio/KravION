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