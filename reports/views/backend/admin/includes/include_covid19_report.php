<?php
//print_r($utilised_accounts);
?>
<script src="http://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js"></script>
<?php if(count($report_result)>0) {?>
<table id='tbl_covid19' class='table table-striped datatable'>
      <thead>
        <tr>

          <th rowspan='2' style="background-color:gray; color:white;"><?= get_phrase('cluster'); ?></th>
          <!-- Draw the Support modes in  a table -->

          <?php 
          $footer_count_of_tds=0;
          
          foreach ($utilised_accounts as $support_mode=>$accounts) { 
            
            $footer_count_of_tds+=count($accounts);
            
            ?>
 
            <th colspan='<?=count($accounts);?>' style="background-color:<?=rand_color();?>; color:white;"><?= $support_mode; ?></th>

          <?php } ?>
          <th rowspan='2' style="background-color:gray; color:white;"><?= get_phrase('Total'); ?></th>
        </tr>
        
        <tr>
        <?php foreach($utilised_accounts as $support_mode=> $accounts){
          
          foreach($accounts as $account){
          
          ?>

          <th><b><?=$account;?></b></th>

        <?php } }?>

         

        </tr>

      </thead>
      <tbody>
       <?php foreach($report_result as $cluster =>$support_modes_and_accounts){?>
        <tr>
          <td style="background-color:gray; color:white;"><?=$cluster;?></td>
          <?php foreach($utilised_accounts as $utilised_support_mode =>$accounts){
             foreach($accounts as $account){
               $count_of_grouped_elements=0;
               if(isset($support_modes_and_accounts[$utilised_support_mode][$account])){
                $count_of_grouped_elements=$support_modes_and_accounts[$utilised_support_mode][$account];
               }
            ?>
            <!-- <td><a target='__blank' href='<?php echo base_url();?>reports.php/<?=$this->session->login_type;?>/covid19_vouchers/<?=$cluster;?>/<?=$utilised_support_mode;?>/<?=$account;?>'><?=$count_of_grouped_elements?></a></td> -->
            <td><a href='#'><?=$count_of_grouped_elements?></a></td>
          <?php } }?>
          <td></td>
        </tr>

       <?php }?>

      </tbody>
      <tfoot>
        <tr>
        <!-- Draw the footer th -->
          <th style="background-color:gray; color:white;">Grand Totals:</th>

          <?php for($i=0;$i<$footer_count_of_tds;$i++){ ?>
            <th><b>0.0</b></th>
          <?php } ?>

          <th>0.0</th>
        </tr>

      </tfoot>
    </table>

          <?php } else{?>

          <div class='well'>Data Not Available in specified month</div>


          <?php }?>

    <script >
    
      $(document).ready(function() {

//         var datatable= $(".datatable").table2excel({
// 					exclude: ".noExl",
// 					name: "Excel Document Name",
// 					filename: "myFileName",
// 					exclude_img: true,
// 					exclude_links: true,
// 					exclude_inputs: true
// });

    $(".datatable").DataTable(

        {
            orderCellsTop: true,
            buttons: [
		         //'csv', 'excel', 'print'

            { extend: 'excelHtml5', footer: true },
            { extend: 'copyHtml5', footer: true },
            { extend: 'csvHtml5', footer: true },
            { extend: 'pdfHtml5', footer: true },

                 
		    ],	      
		    stateSave: true,

        dom: '<"row"l><Bf><"col-sm-12"rt><ip>',
        bPaginate: false

        }
    );

    //Find the table tr and loop all the tr
    var tbody_rows = $('#tbl_covid19 tbody tr');

    $.each(tbody_rows, function(i, tr) {

      var tds = $(tr).find('td');
      var total = 0;

      //Loop each td as you sum up the inner html
      $.each(tds, function(x, td) {
        if (x != 0) {

          total += Number($(td).html());
        }

      });
      //Display the total of all td to last td
      $(tr).find('td').last().html(total.toFixed(2));
      $(tr).find('td:last-child').css({backgroundColor:'gray',color:'white'});
      

    });

    //Sum vertically each row
    $('#tbl_covid19  thead th').each(function(index) {
      calculateColumnTotals(index);
    });

  });

  //Calculate the totals for tds
  function calculateColumnTotals(index) {
    var total = 0;
    $('#tbl_covid19  tr').each(function() {
      var value = Number($('td', this).eq(index).html());
      if (!isNaN(value)) {
        total += value;
      }
    });
    if (total != 0) {

      $('#tbl_covid19 tfoot th').eq(index).html('<b>' + total.toFixed(2) + '</b>');
      $('#tbl_covid19 tfoot th:last-child').css({backgroundColor:'gray',color:'white'});
    }

  }

    
    </script>