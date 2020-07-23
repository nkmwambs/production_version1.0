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
              <button class='btn btn-primary'><?=get_phrase('load_report');?></button>
            </div>

        </div>



    </div>
</div>
<hr/>
<div class='row'>
    <div class='col-xs-12'>

    <table id='tbl_covid19' class='table table-striped datatable'>
    <thead>
    <tr>
      <th rowspan='2'><?=get_phrase('cluster');?></th>
      <th colspan='3'>UDCT Via MPesa</th>
      <th colspan='3'>Food Baskets</th>
      <th colspan='3'>Hygiene Kits</th>
      <th rowspan='2'><?=get_phrase('Total');?></th>
    </tr>
    
    <tr >
      <th>E45</th>
      <th>E320</th>
      <th>E200</th>

      <th>E45</th>
      <th>E320</th>
      <th>E200</th>

      <th>E45</th>
      <th>E320</th>
      <th>E200</th>
      
    </tr>
    
    </thead>
    <tbody>
    <tr>
    <td>Kiambu</td>
    <td>23</td>
    <td>20</td>
    <td>345</td>
    <td>34</td>
    <td>4</td>
    <td>566</td>
    <td>44</td>
    <td>6</td>
    <td>8</td>
    <td></td>
    
    </tr>

    <tr>
    <td>Lake Basin</td>
    <td>12</td>
    <td>2</td>
    <td>33</td>
    <td>4</td>
    <td>56</td>
    <td>23</td>
    <td>23</td>
    <td>12</td>
    <td>98</td>
    <td></td>
    
    </tr>

    <tr>
    <td>Mombasa</td>
    <td>111</td>
    <td>2</td>
    <td>3</td>
    <td>45</td>
    <td>12</td>
    <td>12</td>
    <td>45</td>
    <td>23</td>
    <td>5</td>
    <td></td>
    
    </tr>
    
    </tbody>
    <tfoot></tfoot>
    </table>



    </div>

</div>
<script>

 $(document).ready(function(){

   var tbody=$('#tbl_covid19 tbody');
   
   var tbody_rows=$('#tbl_covid19 tbody tr')

   $.each(tbody_rows, function(i, tr){
     var tds=$(tr).find('td');
     var total=0;
     var size_of_tds=tds.length;

     $.each(tds,function(x,td){
         if(x!=0) {
          total =parseInt(total) + parseInt($(td).html());

         }
        

     });
     
     $(tr).find('td').last().html(total);

   });

 });

</script>