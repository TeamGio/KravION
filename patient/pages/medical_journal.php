<?php
// Hämta alla pekare (Medical Records)
$records = $erp_client->getMedicalrecords($patient_erp_id);
?>

<div class="card">
    <h3><?php echo $t['medical_journal']; ?></h3>
    <p style="color:#666; font-size:0.9em; margin-bottom:20px;">
        <?php echo $t['click_row_info']; ?>
    </p>

    <?php if (!empty($records)): ?>
        <?php foreach ($records as $record): ?>
            <?php
                // 1. Hämta basinfo från själva pekaren
                $date_raw = $record['creation'] ?? ''; 
                $date_display = $date_raw ? date('Y-m-d', strtotime($date_raw)) : 'Datum saknas';
                $status = $record['status'] ?? '';
                
                // 2. Identifiera vart den pekar (Referens)
                // Fältnamnen i ERPNext är oftast 'reference_doctype' och 'reference_name'
                $ref_doctype = $record['reference_doctype'] ?? '';
                $ref_name    = $record['reference_name'] ?? '';

                $actual_data = []; // Här ska vi spara den riktiga datan
                
                // 3. GÖR DET EXTRA ANROPET HÄR
                if (!empty($ref_doctype) && !empty($ref_name)) {
                    $actual_data = $erp_client->getDoc($ref_doctype, $ref_name);
                }
            ?>

            <details style="margin-bottom:15px; background: white; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                
                <summary style="padding:15px; cursor:pointer; background-color:#f8f9fa; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:600; color:#333;">
                        <span style="color:#007bff; margin-right:10px; font-size: 1.1em;">
                             <?php echo htmlspecialchars($date_display); ?>
                        </span>

                    </div>
                    
                    <span style="font-size:0.8em; color:#999;">▼ Läs mer</span>
                </summary>

                <div style="padding:25px; border-top:1px solid #e0e0e0; background-color: #fff;">
                    


                    <?php if (!empty($actual_data)): ?>
                        
                        <table class="table table-striped" style="width:100%; font-size:0.9em; border:1px solid #eee;">
                            <?php foreach ($actual_data as $key => $value): ?>
                                <?php 
                                   
                                    // Filtrera bort ointressanta systemfält + de fält du ville dölja (Title, Patient, Company etc.)
                                    if (in_array($key, [
                                        'name', 'owner', 'creation', 'modified', 'modified_by', 'docstatus', 'idx', 'doctype', 
                                        'naming_series', 'title', 'patient', 'patient_name', 'company'])) continue;
                                    
                                    // Om värdet är tomt, hoppa över
                                    if (empty($value)) continue;

                                    // Om det är en array (t.ex. tabellrader), visa som JSON eller text
                                    if (is_array($value)) $value = '[Tabell/Data]';
                                ?>
                                <tr>
                                    <td style="font-weight:bold; width:30%; padding:5px; border-bottom:1px solid #eee;">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?>
                                    </td>
                                    <td style="padding:5px; border-bottom:1px solid #eee;">
                                        <?php echo htmlspecialchars($value); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                    <?php else: ?>
                        <div class="alert alert-warning">
                            Kunde inte hämta detaljerna för det länkade dokumentet (<?php echo htmlspecialchars($ref_doctype); ?>).
                        </div>
                    <?php endif; ?>

                </div>
            </details>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info" style="text-align:center; padding:30px;">
            <h4>Inga journaler hittades.</h4>
        </div>
    <?php endif; ?>
</div>