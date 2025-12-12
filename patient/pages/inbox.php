<div class="card">
    <h3>Min Inkorg</h3>


    <?php
    $new_lang = ($lang === 'sv') ? 'en' : 'sv';


    $messages = $erp_client->getMessagesForPatient($patient_erp_id);
    if (count($messages) > 0):
    ?>
  <?php echo $t['date']; ?>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 10px; width: 20%;">  <?php echo $t['practitioner']; ?></th>
                    <th style="padding: 10px; width: 40%;">  <?php echo $t['subject']; ?></th>
                    <th style="padding: 10px; width: 20%;">  <?php echo $t['date']; ?></th>
                    <th style="padding: 10px; width: 10%;">  <?php echo $t['status']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 10px;">
                            <strong><?php echo htmlspecialchars($msg['practitioner'] ?? 'System'); ?></strong>
                        </td>


                        <td style="padding: 10px;">
                            <?php echo htmlspecialchars($msg['subject'] ?? '(Inget ämne)'); ?>
                        </td>


                        <td style="padding: 10px;">
                            <?php
                            // Vi formaterar datumet (ÅÅÅÅ-MM-DD kl TT:MM)
                            $date = strtotime($msg['creation'] ?? 'now');
                            echo date('Y-m-d H:i', $date);
                            ?>
                        </td>


                        <td style="padding: 10px;">
                            <span style="font-size: 0.8em; color: #666;">(Klicka nedan)</span>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="5" style="padding: 0; border: none;">
                            <details style="padding: 10px 20px; background: #f9f9f9; border-bottom: 2px solid #eee;">
                                <summary style="cursor: pointer; color: #007bff; font-weight: bold; margin-bottom: 10px;">Läs meddelande</summary>
                                <div style="background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                                    <?php echo $msg['message'] ?? 'Inget innehåll.'; ?>
                                </div>
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