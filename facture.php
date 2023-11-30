<?php

session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if($_SESSION['depot'] == "SEC"){
        if(isset($_GET['p']) && $_GET['p'] == 1)
            header("Location:facturep.php?p=1");
        else
            header("Location:facture_entrer.php");
    }elseif($_SESSION['depot'] != "H"){
      header("Location:facturep.php");
    }

    if(isset($_GET['DO_Piece'])){

        $numBon = $_GET['DO_Piece'];

        $sql = "UPDATE facture_entete
                SET prepa = 1
                WHERE DO_Piece = '".$numBon."'";

        $c= $dbh->query($sql);
        // var_dump($data);

        header("Location:facture.php");
    }

    if(isset($_GET['date_debut'])){
        $current_date_time = date_create($_GET['date_debut']);
        $user_current_date = date_format($current_date_time, "Y-m-d");



            $tsql = "SELECT DISTINCT TOP 50  (DO_Piece),DO_Ref,lineStat,
            CASE 
                 WHEN DO_Tiers = '29' THEN 'SANIFER II'
                WHEN DO_Tiers = '30' THEN 'SANIFER II'
                WHEN DO_Tiers = '27' THEN 'SANIFER III'
                WHEN DO_Tiers = '32' THEN 'SANIFER III'
                ELSE CT_Intitule
            END AS CT_Intitule
            ,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
                    dbo.facture_ligne.DO_Piece,
                    dbo.facture_entete.statut AS entStat,
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_ligne.statut AS lineStat,
                    dbo.facture_ligne.AR_Ref,
                    dbo.facture_ligne.DL_Qte,
                    dbo.facture_ligne.DO_Date AS lineDate,
                    dbo.facture_ligne.DL_Design,
                    dbo.article.AR_Design,
                    dbo.article.DL,
                    dbo.facture_entete.DO_Tiers,
                    dbo.facture_entete.prepa,
                    dbo.facture_entete.DO_Coord01,
                    dbo.facture_entete.DO_Coord02,
                    dbo.facture_entete.DO_Coord03,
                    dbo.facture_entete.DO_Coord04,
                    dbo.client.CT_Intitule,
                    dbo.client.CT_Adresse
                    FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    LEFT JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                    WHERE
                    ".$_SESSION['where']." 
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_ligne.statut = 0 AND dbo.facture_entete.DO_Provenance IN(0,3))
                    AS tabEnt

                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "'
                    ORDER BY entDate DESC";



        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {

            $sql = "SELECT * FROM dbo.facture_ligne
                    INNER JOIN dbo.facture_entete ON dbo.facture_ligne.DO_Piece = dbo.facture_entete.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    WHERE
                    dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' AND dbo.article.DL = '".$_SESSION['depot']."' ";
            $get = $dbh->query($sql);
            while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                $d[] = $rowA;
            }

            $data[] = $row;

        }

        $val =  json_encode($data);
        $articles = json_encode($d);

    }elseif(isset($_GET['blfacture'])){

        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

        $tsql = "SELECT DISTINCT TOP 50  (DO_Piece),DO_Ref,
        CASE 
                 WHEN DO_Tiers = '29' THEN 'SANIFER II'
                WHEN DO_Tiers = '30' THEN 'SANIFER II'
                WHEN DO_Tiers = '27' THEN 'SANIFER III'
                WHEN DO_Tiers = '32' THEN 'SANIFER III'
                ELSE CT_Intitule
            END AS CT_Intitule
        ,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
                    dbo.facture_ligne.DO_Piece,
                    dbo.facture_entete.statut AS entStat,
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_ligne.statut AS lineStat,
                    dbo.facture_ligne.AR_Ref,
                    dbo.facture_ligne.DL_Qte,
                    dbo.facture_ligne.DO_Date AS lineDate,
                    dbo.facture_ligne.DL_Design,
                    dbo.article.AR_Design,
                    dbo.article.DL,
                    dbo.facture_entete.DO_Tiers,
                    dbo.facture_entete.prepa,
                    dbo.facture_entete.DO_Coord01,
                    dbo.facture_entete.DO_Coord02,
                    dbo.facture_entete.DO_Coord03,
                    dbo.facture_entete.DO_Coord04,
                    dbo.client.CT_Intitule,
                    dbo.client.CT_Adresse
                    FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    LEFT JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                    )
                    AS tabEnt

                    WHERE
                    DO_Piece = '".$_GET['blfacture']."'";

                    $getData = $dbh->query($tsql);
                    

                while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {

                    $sql = "SELECT * FROM dbo.facture_ligne
                            INNER JOIN dbo.facture_entete ON dbo.facture_ligne.DO_Piece = dbo.facture_entete.DO_Piece
                            INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                            WHERE
                            dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' AND dbo.article.DL = '".$_SESSION['depot']."'";
                    $get = $dbh->query($sql);
                    while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                        $d[] = $rowA;
                    }

                    $data[] = $row;

                }

                $val =  json_encode($data);
                $articles = json_encode($d);

                
        
    }else{
        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT DISTINCT TOP 10  (DO_Piece) FROM (SELECT
                facture_entete.DO_Piece,
                facture_ligne.AR_Ref,
                article.DL,
                facture_ligne.statut AS lineStat,
                facture_entete.statut AS entStat,
                facture_entete.DO_Date

                FROM
                facture_entete
                INNER JOIN facture_ligne ON facture_entete.DO_Piece = facture_ligne.DO_Piece
                INNER JOIN article ON facture_ligne.AR_Ref = article.AR_Ref_New
                WHERE facture_entete.DO_Provenance IN(0,3))
                AS TabENT
                WHERE DO_Date = '" . $user_current_date. "'";
        }else{

            $tsql = "SELECT DISTINCT TOP 50  (DO_Piece),DO_Ref,lineStat,
            CASE 
                 WHEN DO_Tiers = '29' THEN 'SANIFER II'
                WHEN DO_Tiers = '30' THEN 'SANIFER II'
                WHEN DO_Tiers = '27' THEN 'SANIFER III'
                WHEN DO_Tiers = '32' THEN 'SANIFER III'
                ELSE CT_Intitule
            END AS CT_Intitule
            ,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
                    dbo.facture_ligne.DO_Piece,
                    dbo.facture_entete.statut AS entStat,
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_ligne.statut AS lineStat,
                    dbo.facture_ligne.AR_Ref,
                    dbo.facture_ligne.DL_Qte,
                    dbo.facture_ligne.DO_Date AS lineDate,
                    dbo.facture_ligne.DL_Design,
                    dbo.article.AR_Design,
                    dbo.article.DL,
                    dbo.facture_entete.DO_Tiers,
                    dbo.facture_entete.prepa,
                    dbo.facture_entete.DO_Coord01,
                    dbo.facture_entete.DO_Coord02,
                    dbo.facture_entete.DO_Coord03,
                    dbo.facture_entete.DO_Coord04,
                    dbo.client.CT_Intitule,
                    dbo.client.CT_Adresse
                    FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    LEFT JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                    WHERE
                    ".$_SESSION['where']." 
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_ligne.statut = 0 AND dbo.facture_entete.DO_Provenance IN(0,3))
                    AS tabEnt

                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "'
                    ORDER BY entDate DESC";

        }


        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {

            $sql = "SELECT * FROM dbo.facture_ligne
                    INNER JOIN dbo.facture_entete ON dbo.facture_ligne.DO_Piece = dbo.facture_entete.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    WHERE
                    dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' AND dbo.article.DL = '".$_SESSION['depot']."' ";
            $get = $dbh->query($sql);
            while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                $d[] = $rowA;
            }

            $data[] = $row;

        }

        /*var_dump($data);
        echo "-------------------------";*/
        // var_dump($d);
        
        $val =  json_encode($data);
        $articles = json_encode($d);

    }


}
?>

<?php include('includes/entete.php'); ?>
    <div class="row">
        <div class="col-md-12"  ng-app="dynamicApp" ng-controller="dynamicCtrl" class="container" ng-init="fetchData()">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5>Préparation</h5>
                            <!--<a href="prepaFini.php" class="pull-right btn btn-success livrFini" style="margin-top: -32px;">Livraison fini</a>-->
                        </div>


                        <div class="panel-body">
                            <div class="col-sm-5" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">N° Facture :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control numFact" ng-model="numFacture" id="blfacture" name="blfacture" onKeyPress="if(event.keyCode == 13) validerForm()";/>
                                    </div>
                                </div>
                            </div>
                            <form method="get" action="facture.php" class="form-horizontal" enctype="multipart/form-data">

                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Date du :</label>
                                        <div class="col-sm-8">
                                            <input id="datepicker_debut" name="date_debut" value="<?php if(isset($_GET['date_debut'])){ echo $_GET['date_debut']; }else{ echo $current_date_time->format("Y-m-d"); } ?>"/>
                                        </div>
                                    </div>
                                </div>

                                <!--   <div class="col-sm-5">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Fin du :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_fin" class="form-control"  name="date_fin" value="<?php if(isset($dateFin)){ echo date_format($d_f,"Y-m-d"); } ?>"/>
                                                </div>
                                            </div>
                                        </div>-->

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="col-sm-8 col-sm-offset-2">
                                            <button class="btn btn-primary" name="rechercher" type="submit">Rechercher</button>

                                        </div>
                                    </div>
                                </div>
                            </form>

</div>
                                <!--Drop down -->
                                <div class="enteteDrop">
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">N° Facture</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">Client</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-4">Commentaire</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">Date du commande</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-1">Status</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-1">Action</span>
                                </div>

                                <div ng-repeat="data in namesData | filter:numFacture">
                                    <div class="containerDrop">


                                        <div class="card">
                                            <div class="card-header" id="headingThree">
                                                <h2 class="mb-0">
                                                    <button style="width: 1300px;text-align: left;font-size: 13px;" class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#{{ data.DO_Piece }}" aria-expanded="false" aria-controls="{{ data.DO_Piece }}">

                                                        <span class="col-md-2 test">{{ data.DO_Piece }}</span>

                                                        <span class="col-md-1">{{ data.DO_Tiers }} - {{ data.CT_Intitule }}</span>

                                                        <span class="col-md-5">{{ data.DO_Coord01 }} | {{ data.DO_Coord02 }} | {{ data.DO_Coord03 }} | {{ data.DO_Coord04 }}</span>


                                                        <span class="col-md-2">{{data.entDate| toDate | date:'dd/MM/yyyy HH:mm'}}</span>
                                                        <span class="col-md-1">
                                                            <span ng-if="data.prepa=='0'">
                                                               <img src="images/status0.png" style="margin-left: 10px;"/>
                                                           </span>
                                                           <span ng-if="data.prepa=='1'">
                                                               <img src="images/status1.png" style="margin-left: 10px;"/>
                                                           </span>
                                                        </span>
                                                        <span class="col-md-1"><a href="gestion.php?numBont={{ data.DO_Piece }}">Details</a></span>


                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="{{ data.DO_Piece }}" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                                <div class="card-body">
                                                     <table id="articleFactureDrop" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                        <thead>
                                                        <tr style="background: #93c54b;color: #fff;font-size: 12px;">
                                                            <th>N° Article</th>
                                                            <th>Description</th>
                                                            <th>Qte Commandé</th>
                                                            <th>Dépot</th>

                                                        </tr>
                                                        </thead>

                                                        <tbody>

                                                            <tr ng-repeat="article in articleData | filter:data.DO_Piece">

                                                                    <td>{{ article.AR_Ref_New }}</td>
                                                                    <td>{{ article.DL_Design }}</td>
                                                                    <td>{{ article.DL_Qte | number : fractionSize }}</td>
                                                                    <td>{{ article.DL }}</td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                    <span style="margin-left: 55px;">
                                                         <a href="facture.php?DO_Piece={{ data.DO_Piece }}" class="artAction btn btn-success">Tout préparer</a>
                                                          <input type="submit" style="margin-right: 10px;" name="imprimer" value="Imprimer" class="btn btn-info imprimer" onclick="window.print();return false;">
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="js/angular.js"></script>
    <script type="text/javascript">
        var app = angular.module('dynamicApp', []);
        app.controller('dynamicCtrl', function($scope, $http){
            $scope.fetchData = function(){
                $http.get('facture.php').success(function(data){
                    $scope.namesData = <?php echo $val; ?>;
                    // console.log($scope.namesData);
                });

                $http.get('facture.php').success(function(data){
                    $scope.articleData = <?php echo $articles; ?>;
                    // console.log($scope.namesData);
                });
            };



            $scope.insertSortie = function(){
                var request = $http({
                    method: "post",
                    url: "insertHistory.php",
                    data: {
                        do_piece: $scope.numFacture,

                    },
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).then(function(data, status, headers, config) {
                    console.log(data);
                }, function(data, status, headers, config) {
                    //an error occurred
                });

            }

            $scope.query = "";
        });

        app.filter('toDate', function() {
                      return function(items) {
                        return new Date(items);
                      };
                    });

        /* rafraichir page tous les 5min */

        function refresh(time)
        {
          setTimeout(function () { window.location.reload(); }, time*1000);
        }
        refresh(120);
        
        /* validation formulaire BL */

        function validerForm(){
            console.log("ok");
                  
            var blfacture = $("#blfacture").val();
        
           window.location.href = "facture.php?blfacture="+blfacture;
        }

    </script>

<?php include('includes/footer.php'); ?>