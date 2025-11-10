<?php
      $pdo = new PDO('mysql:dbname=Kravion - Mölndals vårdcentral;host=localhost', "sqllab", 'Armadillo#2025');
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);