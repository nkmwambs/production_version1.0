<?php


print_r($report_result);
?>
<div class='row'>
  <div class='col-xs-12'>

    <div class='form-group'>
      <label class='control-label col-xs-2'><?= get_phrase('choose_grouping'); ?></label>
      <div class='col-xs-8'>
        <select class='form-control' id='report_grouping'>
          <option value=''><?= get_phrase('select_grouping'); ?></option>
          <option value='beneficiary'><?= get_phrase('beneficiary_count'); ?></option>
          <option value='household'><?= get_phrase('household_count'); ?></option>
          <option value='fcp'><?= get_phrase('fcp_count'); ?></option>
          <option value='amount'><?= get_phrase('amount_spent'); ?></option>
        </select>
      </div>
      <div class='col-xs-2'>
        <button class='btn btn-primary'><?= get_phrase('load_report'); ?></button>
      </div>

    </div>



  </div>
</div>
<hr />
<div class='row'>
  <div class='col-xs-12'>

    <table id='tbl_covid19' class='table table-striped datatable'>
      <thead>
        <tr>

          <th rowspan='2' style="background-color:gray; color:white;"><?= get_phrase('cluster'); ?></th>
          <!-- Draw the Support modes in  a table -->
          <?php foreach ($utilised_accounts as $support_mode=>$accounts) { ?>
 
            <th colspan='<?=count($accounts);?>' style="background-color:<?=rand_color();?>; color:white;"><?= $support_mode; ?></th>

          <?php } ?>
          <th rowspan='2'><?= get_phrase('Total'); ?></th>
        </tr>
        
        <tr>
        <?php foreach($utilised_accounts as $support_mode=> $accounts){
          
          foreach($accounts as $account){
          
          ?>

          <th><?=$account;?></th>

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
            <td><?=$count_of_grouped_elements?></td>
          <?php } }?>
          <td></td>

          

        </tr>

       <?php }?>

        

      </tbody>
      <tfoot>
        <tr>
          <th style="background-color:gray; color:white;">Grand Totals:</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
          <th>0.0</th>
        </tr>

      </tfoot>
    </table>



  </div>

</div>
<script>
  $(document).ready(function() {

    //Find the table tr and loop all the tr
    var tbody_rows = $('#tbl_covid19 tbody tr')

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
      $(tr).find('td').last().html(total);

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

      $('#tbl_covid19 tfoot th').eq(index).html('<b>' + total + '</b>');
    }

  }
</script>