<?php 
$this->load->library('media_storage');
?>
<style>
    
    .container {
        border: 2px solid black;
        padding: 10px;
        margin-bottom: 10px;
        justify-content: center;
        max-width: 1000px;
    }
    .header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .logo {
        width: 60px;
        height: 60px;
        background-color: #00a0e4;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .college-info {
        text-align: center;
        flex-grow: 1;
    }
    h1 {
        color: #006400;
        margin: 0;
        font-size: 24px;
    }
    .college-details {
        font-size: 0.8em;
        margin: 2px 0;
    }
    .exam-info {
        text-align: center;
        font-size: 1.1em;
        font-weight: bold;
        margin: 10px 0;
        color: #8B4513;
    }
    .student-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.8em;
    }
    .student-info div {
        flex-basis: 30%;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.6em;
    }
    th, td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
    .signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 50px;
        font-size: 0.9em;
    }
</style>


<?php 
foreach ($students as $student) {
    
    $i++;
?>

    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="<?php echo $this->media_storage->getImageURL('uploads/halltickectgeneration/logo/'. $certificate[0]->logo_path);?>" width="80" height="80" alt="Amaravathi Junior College Logo"/>
            </div>
            <div class="college-info">
                <h1><?php echo  $certificate[0]->schoolname?></h1>
                <p class="college-details"><?php echo  $certificate[0]->address?></p>
                <p class="college-details"><?php echo  $certificate[0]->email?></p>
                <p class="college-details"><?php echo  $certificate[0]->phone?></p>
            </div>
        </div>
        
        <div class="exam-info"><?php echo  $certificate[0]->examheading?></div>
        
        <div class="student-info">
            <div><strong>CLASS:</strong> <?php echo $student->class; ?></div>
            <div><strong>SECTION:</strong> <?php echo $student->section;?></div>
            <div><strong>NAME:</strong><?php echo $student->name;?></div>
        </div>
        
        <div class="student-info">
            
            <div>
                <?php 
                    $toplefttxt = $certificate[0]->toplefttext;
                    $toplefttxt = str_replace('[application_no]', $student->admission_no, $toplefttxt);
                    echo $toplefttxt;
                ?>
            </div>
            <div>
                <?php 
                    $topmiddletext = $certificate[0]->topmiddletext;
                    $topmiddletext = str_replace('[application_no]', $student->admission_no, $topmiddletext);
                    echo $topmiddletext;
                ?>
            </div>
            <div>
                <?php 
                    $toprighttext = $certificate[0]->toprighttext;
                    $toprighttext = str_replace('[application_no]', $student->admission_no, $toprighttext);
                    echo $toprighttext;
                ?>
            </div>
            
        </div>
        
        
        <table>
            <tr>
                <th>SUBJECT</th>

                <?php 
                foreach ($hallsubgrp as $subject) {
                
                ?>

                <th><?php echo $subject['name'];?></th>

                <?php
                }
                
                ?>
                <!-- <th>ENGLISH PAPER -II</th>
                <th>MATHEMATICS IIB</th>
                <th>MATHEMATICS IIA</th>
                <th>CHEMISTRY PAPER II</th>
                <th>LANGUAGE PAPER -II</th>
                <th>PHYSICS PAPER II</th> -->
            </tr>
            
            <tr>
                <td>DATE</td>
                <?php 
                foreach ($hallsubgrp as $subject) {
                
                ?>

                <td><?php echo $subject['date'];?></td>

                <?php
                }
                
                ?>
                
               
            </tr>
            <tr>
                <td>TIME & MARKS</td>
                <?php 
                foreach ($hallsubgrp as $subject) {
                
                ?>

                <td><?php echo $subject['starttime'];?> TO <?php echo $subject['endtime'];?><br>MAX-MARKS : <?php echo $subject['maxmark'];?><br>MIN-MARKS : <?php echo $subject['minmark'];?></td>

                <?php
                }
                
                ?>
                
            </tr>
            
            <tr>
                <td>INVIGILATOR'S SIGN</td>
                <?php 
                foreach ($hallsubgrp as $subject) {
                
                ?>

                    <td></td>
                <?php
                }
                
                ?>
            </tr>
        </table>
        
        <div class="signatures">
            <div><?php echo  $certificate[0]->bottomlefttext?></div>
            <div><?php echo  $certificate[0]->bottommiddletext?></div>
            <div><?php echo  $certificate[0]->bottomrighttext?></div>
        </div>
    </div>
<?php 
    }
?>