<style>

    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        padding: 20px;
    }
    .container {
        border: 2px solid black;
        padding: 10px;
        max-width: 800px;
        margin: 0 auto;
    }
    .header {
        display: flex;
        flex-wrap: wrap;
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
        font-size: 1.5em;
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
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.9em;
    }
    .student-info div {
        flex-basis: 100%;
        margin-bottom: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8em;
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
        flex-wrap: wrap;
        justify-content: space-between;
        margin-top: 30px;
        font-size: 0.9em;
    }
    .signatures div {
        flex-basis: 100%;
        text-align: center;
        margin-bottom: 10px;
    }

    @media screen and (min-width: 600px) {
        .student-info div {
            flex-basis: 30%;
        }
        .signatures div {
            flex-basis: 30%;
        }
    }

    @media screen and (max-width: 600px) {
        table {
            font-size: 0.7em;
        }
        th, td {
            padding: 2px;
        }
    }
</style>

<div class="container">
    <div class="header">
        <!-- <div class="logo">
            <img src="<?php echo $this->media_storage->getImageURL('uploads/halltickectgeneration/logo/'. $idcard->logo_path); ?>" width="100" height="100" alt="Amaravathi Junior College Logo"/>
            <img src="<?php echo $this->media_storage->getImageURL('uploads/tcgeneration/logo/'. $certificate[0]->logo); ?>" width="100" height="100"/>

        </div> -->
        <div style="padding-top: 5px; float: left;">
            <img src="<?php echo $this->media_storage->getImageURL('uploads/halltickectgeneration/logo/'. $idcard->logo_path); ?>" width="80" height="80"/>
        </div>

        <div class="college-info">
            <h1><?php echo $idcard->schoolname; ?></h1>
            <p class="college-details"><?php echo $idcard->address; ?></p>
            <p class="college-details"><?php echo $idcard->email; ?></p>
            <p class="college-details"><?php echo $idcard->phone; ?></p>
        </div>
    </div>
    
    <div class="exam-info"><?php echo $idcard->examheading ; ?></div>
    
    <div class="student-info">
        <div><strong>CLASS/GRADE:</strong> SENIOR MPC EM</div>
        <div><strong>SECTION:</strong> LEO-II</div>
        <div><strong>NAME:</strong> P.HARIKA</div>
    </div>

    <div class="student-info">
        <div><strong><?php echo $idcard->toplefttext;?></strong> </div>
        <div><strong><?php echo $idcard->topmiddletext;?></strong></div>
        <div><strong><?php echo $idcard->toprighttext;?></strong></div>
    </div>
    
    <table>
        <tr>
            <th>SUBJECT</th>
            <th>ENGLISH PAPER -II</th>
            <th>MATHEMATICS IIB</th>
            <th>MATHEMATICS IIA</th>
            <th>CHEMISTRY PAPER II</th>
            <th>LANGUAGE PAPER -II</th>
            <th>PHYSICS PAPER II</th>
        </tr>
        <tr>
            <td>DATE</td>
            <td>22-12-2023</td>
            <td>27-12-2023</td>
            <td>30-12-2023</td>
            <td>04-01-2024</td>
            <td>08-01-2024</td>
            <td>11-01-2024</td>
        </tr>
        <tr>
            <td>TIME & MARKS</td>
            <td>2:30 TO 5:30<br>MAX-MARKS : 100<br>MIN-MARKS : 35</td>
            <td>2:30 TO 5:30<br>MAX-MARKS : 100<br>MIN-MARKS : 35</td>
            <td>2:30 TO 5:30<br>MAX-MARKS : 100<br>MIN-MARKS : 35</td>
            <td>2:30 TO 5:30<br>MAX-MARKS : 100<br>MIN-MARKS : 35</td>
            <td>2:30 TO 5:30<br>MAX-MARKS : 100<br>MIN-MARKS : 35</td>
            <td>2:30 TO 5:30<br>MAX-MARKS : 100<br>MIN-MARKS : 35</td>
        </tr>
        
        
        <tr>
            <td>INVIGILATOR'S SIGN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <div class="signatures">
        <div><?php echo $idcard->bottomlefttext;?></div>
        <div><?php echo $idcard->bottommiddletext;?></div>
        <div><?php echo $idcard->bottomrighttext;?></div>
    </div>
</div>
