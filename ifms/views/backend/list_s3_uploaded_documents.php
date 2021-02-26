    <?php 
    if(empty($uploaded_files)){
    ?>
        <div class="well" style="margin-top:10px;">No files found</div>
    <?php
    }else{
        if($show_as_table){
    ?>
    
    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th><?=get_phrase('action');?></th>
                                <th><?= get_phrase('file_name');?></th>
                                <th><?= get_phrase('upload_date');?></th>
                                <th><?= get_phrase('file_size');?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                
                                foreach($uploaded_files as $file):
                                    $objectKey = $file['attachment_url'].'/'.$file['attachment_name'];
                                    $url = $this->aws_attachment_library->s3_preassigned_url($objectKey);
                            ?>
                                <tr>
                                    <td><i class="fa fa-trash-o delete_file" style="cursor:pointer;"></i></td>
                                    <td><a target="__blank" href="<?=$url;?>"><?= $file['attachment_name'];?></a></td>
                                    <td><?= $file['attachment_created_date'];?></td>
                                    <td><?= number_format(($file['attachment_size']/1000000),2).' MB';?></td>
                                </tr>
                            <?php 
                                endforeach;
                                
                            ?>
                        </tbody>
                    </table>
<?php 
    }else{
?>
        <ul style="list-style-type:none;">
             <?php 
                                
                foreach($uploaded_files as $file):
                    $objectKey = $file['attachment_url'].'/'.$file['attachment_name'];
                    $url = $this->aws_attachment_library->s3_preassigned_url($objectKey);
            ?>                                
                <li><a target="__blank" href="<?=$url;?>"><?= $file['attachment_name'];?></a></li>                                
            <?php 
                endforeach;                    
            ?>
        </ul>
<?php
    }
}
?>