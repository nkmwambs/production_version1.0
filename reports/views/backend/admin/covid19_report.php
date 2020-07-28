<?php


//print_r($utilised_accounts);
//print_r($this->dct_model->covid19_data_query('2019-06-01','fcp'));


?>
<div class='row'>
  <div class='col-xs-12'>

    <div class='form-group'>
      <label class='control-label col-xs-2'><?= get_phrase('choose_grouping'); ?></label>
      <div class='col-xs-6'>
        <select class='form-control' id='report_grouping'>
          <option value=''><?= get_phrase('select_grouping'); ?></option>
          <option value='beneficiary' <?php if($group_report_by == 'beneficiary') echo 'selected';?> ><?= get_phrase('beneficiary_count'); ?></option>
          <option value='household' <?php if($group_report_by == 'household') echo 'selected';?> ><?= get_phrase('household_count'); ?></option>
          <option value='fcp' <?php if($group_report_by == 'fcp') echo 'selected';?> ><?= get_phrase('fcp_count'); ?></option>
          <option value='amount' <?php if($group_report_by == 'amount') echo 'selected';?> ><?= get_phrase('amount_spent'); ?></option>
        </select>
      </div>
      <div class='col-xs-2'>
        <input type='text' class='form-control datepicker' id='reporting_month' data-format="yyyy-mm-dd"  />
      </div>
      <div class='col-xs-2'>
        <button class='btn btn-primary' id='load_report'><?= get_phrase('load_report'); ?></button>
      </div>

    </div>



  </div>
</div>
<hr />
<div class='row'>
  <div class='col-xs-12' id='report_holder'>
    <?php
      include "includes/include_covid19_report.php";
    ?>
  </div>

</div>
<script>
  // $(document).ready(function() {

  //   //var datatable = $(".datatable").DataTable();

  //   //Find the table tr and loop all the tr
  //   var tbody_rows = $('#tbl_covid19 tbody tr');

  //   $.each(tbody_rows, function(i, tr) {

  //     var tds = $(tr).find('td');
  //     var total = 0;

  //     //Loop each td as you sum up the inner html
  //     $.each(tds, function(x, td) {
  //       if (x != 0) {

  //         total += Number($(td).html());
  //       }

  //     });
  //     //Display the total of all td to last td
  //     $(tr).find('td').last().html(total.toFixed(2));
  //     $(tr).find('td:last-child').css({backgroundColor:'gray',color:'white'});
      

  //   });

  //   //Sum vertically each row
  //   $('#tbl_covid19  thead th').each(function(index) {
  //     calculateColumnTotals(index);
  //   });

  // });

  // //Calculate the totals for tds
  // function calculateColumnTotals(index) {
  //   var total = 0;
  //   $('#tbl_covid19  tr').each(function() {
  //     var value = Number($('td', this).eq(index).html());
  //     if (!isNaN(value)) {
  //       total += value;
  //     }
  //   });
  //   if (total != 0) {

  //     $('#tbl_covid19 tfoot th').eq(index).html('<b>' + total.toFixed(2) + '</b>');
  //     $('#tbl_covid19 tfoot th:last-child').css({backgroundColor:'gray',color:'white'});
  //   }

  // }

  $("#load_report").on('click',function(){
    var report_grouping = $("#report_grouping").val();
    var reporting_month = $("#reporting_month").val();

    if(report_grouping !== ""){
      var url = "<?=base_url();?>reports.php/admin/covid19_report";
      var data = {'group_name':report_grouping,'reporting_month':reporting_month};

      $.post(url,data,function(response){
        $('#report_holder').html(response);
      });
    }
    
  });

</script>


<style>
#overlay{
    position: fixed; /* Sit on top of the page content */
    display: none; /* Hidden by default */
    width: 100%; /* Full width (cover the whole page) */
    height: 100%; /* Full height (cover the whole page) */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5); /* Black background with opacity */
    z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
    cursor: pointer; /* Add a pointer on hover */
}

#overlay img {
    position: absolute;
    top: 50%;
    left: 50%;
}
</style>

<div id="overlay"><img src='<?php echo base_url()."uploads/preloader4.gif";?>'/></div>

<script>
$( document ).ajaxSend(function() {
  $("#overlay").css("display","block");
});

$(document).ajaxSuccess(function() {
    $("#overlay").css("display","none");
});

$(document).ajaxError(function(xhr) {
    alert('Error has occurred');
});

$(document).ready(function(){
  $('.datepicker').datepicker({
			format: 'yyyy-mm-dd'
		});
});

</script>