<?php 
    //print_r($dct_data);
    if(count($data) == 1){ // Has only header_keys
?>
    <div class='well well-lg'>No data found</div>
<?php
      
    }else{
?>
<table class='table table-striped datatable'>
            <thead>
                <tr>
                    <th>FCP No.</th>
                    <th>Cluster Name</th>
                    <?php 
                        $header_keys = array_shift($data);
                        foreach($header_keys as $header_key){
                    ?>
                        <th><?=$header_key;?></th>
                    <?php
                        }
                    ?>
                    <th>Total</th>
                </tr>
            </thead>
            <?php 
                //print_r($data);
            ?>
            <tbody>
               <?php 
                    foreach($data as $fcp_no => $array_of_values){
                ?>
                        <tr>
                            <td><?=$fcp_no;?></td>
                            <td><?=$array_of_values['cluster_name'];?></td>
                            <?php foreach($array_of_values['values'] as $array_of_value){?>
                                <td><?=number_format($array_of_value,2);?></td>
                            <?php }?>
                            <td><?=number_format(array_sum($array_of_values['values']),2);?></td>
                        </tr>
                <?php
                    }
               ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>Total</td>
                    <?php 
                        $sum = 0;
                        foreach($header_keys as $key){
                            $sub_total = array_sum(array_column(array_column($data,'values'),$key));
                            $sum += $sub_total;
                    ?>
                        <td><?=number_format($sub_total,2);?></td>
                    <?php 
                        }
                    ?>
                
                    <td><?=number_format($sum,2)?></td>
                </tr>
            </tfoot>
        </table>

<?php 
    }
?>
