
<style>
    .carousel-item>img{
        object-fit:fill !important;
    }
    #carouselExampleControls .carousel-inner{
        height:280px !important;
    }
</style>
<?php 
$brands = isset($_GET['b']) ? json_decode(urldecode($_GET['b'])) : array();
?>
<section class="py-0">
    <div class="container">
    <div class="row" style="margin-top: 3vh">
        <div class="col-lg-2 px-1 text-sm position-sticky ">
            <h4><b>Brands</b></h4>
            <ul class="list-group">
                <a href="" class="list-group-item list-group-item-action">
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" id="brandAll" disabled>
                        <label for="brandAll">
                             All
                        </label>
                    </div>
                </a>
                <?php 
                $qry = $conn->query("SELECT * FROM brands where status =1 order by name asc");
                while($row=$qry->fetch_assoc()):
                ?>
                <li class="list-group-item list-group-item-action">
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" id="brand-item-<?php echo $row['id'] ?>" <?php echo in_array($row['id'],$brands) ? "checked" : "" ?> class="brand-item" value="<?php echo $row['id'] ?>">
                        <label for="brand-item-<?php echo $row['id'] ?>">
                                <?php echo $row['name'] ?>
                        </label>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="col-lg-10 py-2">
            <div class="row">
                <div class="col-md-12">
                    <div id="carouselExampleControls" class="carousel slide bg-dark" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php 
                                $upload_path = "uploads/banner";
                                if(is_dir(base_app.$upload_path)): 
                                $file= scandir(base_app.$upload_path);
                                $_i = 0;
                                    foreach($file as $img):
                                        if(in_array($img,array('.','..')))
                                            continue;
                                $_i++;
                                    
                            ?>
                            <div class="carousel-item h-100 <?php echo $_i == 1 ? "active" : '' ?>">
                                <img src="<?php echo validate_image($upload_path.'/'.$img) ?>" class="d-block w-100  h-100" alt="<?php echo $img ?>">
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        </div>
                </div>
            </div>
            <div class="container px-4 px-lg-5 mt-5">
                <div class="col mb-5">
                    <button style="width: 60vw;height: 20vh;display:flex;justify-content: center;text-align: center"
                       class="card product-item text-reset text-decoration-none"
                       id="login-button">
                        <h3 style="align-self: center">Want to be eligible for promotions? Join us!</h3>
                    </button>
                </div>
                <h2 class="fw-normal">Our main products</h2>
                <div class="row gx-4 gx-lg-4 row-cols-md-3 row-cols-xl-4 ">
                    <?php 
                        $where = "";
                        if(count($brands)>0)
                        $where = " and p.brand_id in (".implode(",",$brands).") " ;
                        $products = $conn->query("SELECT p.*,b.name as bname,c.category FROM `products` p inner join brands b on p.brand_id = b.id inner join categories c on p.category_id = c.id where p.status = 1 {$where}");
                        while($row = $products->fetch_assoc()):
                            $upload_path = base_app.'/uploads/product_'.$row['id'];
                            $img = "";
                            if(is_dir($upload_path)) {
                                $fileO = scandir($upload_path);
                                if(isset($fileO[2]))
                                    $img = "uploads/product_".$row['id']."/".$fileO[2];
                            }
                            foreach($row as $k=> $v){
                                $row[$k] = trim(stripslashes($v));
                            }
                            $inventory = $conn->query("SELECT distinct(`price`) FROM inventory where product_id = ".$row['id']." order by `price` asc");
                            $inv = array();
                            while($ir = $inventory->fetch_assoc()){
                                $inv[] = format_num($ir['price']);
                            }
                            $price = '';
                            if(isset($inv[0]))
                            $price .= $inv[0];
                            if(count($inv) > 1){
                            $price .= " ~ ".$inv[count($inv) - 1];
                            }
                    ?>
                    <div class="col mb-5" style="width: 20vw">
                        <a style="height: 60vh"
                           class="card product-item text-reset text-decoration-none"
                           href=".?p=view_product&id=<?php echo md5($row['id']) ?>">
                            <!-- Product image-->
                            <div class="overflow-hidden shadow product-holder">
                            <img class="card-img-top w-95 product-cover" src="<?php echo validate_image($img) ?>" alt="..." />
                            </div>
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder d-none d-lg-block"><?php echo $row['name'] ?></h5>
                                    <!-- Product price-->
                                    <span><b class="text-muted d-none d-lg-block">Price: </b><p class="d-none d-lg-block">
                                            <?php echo $price ?></p></span>
                                </div>
                                <p class="m-0"><small><span class="text-muted d-none d-lg-block">Brand:</span>
                                    <p class="d-none d-lg-block"><?php echo $row['bname'] ?></p></small></p>
                                <p class="m-0"><small><span class="text-muted d-none d-lg-block">Category:</span>
                                <p class="d-none d-lg-block"><?php echo $row['category'] ?></p></small></p>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<script>
    function _filter(){
        var brands = []
            $('.brand-item:checked').each(function(){
                brands.push($(this).val())
            })
        _b = JSON.stringify(brands)
        var checked = $('.brand-item:checked').length
        var total = $('.brand-item').length
        if(checked == total)
            location.href="./?";
        else
            location.href="./?b="+encodeURI(_b);
    }
    function check_filter(){
        var checked = $('.brand-item:checked').length
        var total = $('.brand-item').length
        if(checked == total){
            $('#brandAll').attr('checked',true)
        }else{
            $('#brandAll').attr('checked',false)
        }
        if('<?php echo isset($_GET['b']) ?>' == '')
            $('#brandAll,.brand-item').attr('checked',true)
    }
    $(function(){
        check_filter()
        $('#brandAll').change(function(){
            if($(this).is(':checked') == true){
                $('.brand-item').attr('checked',true)
            }else{
                $('.brand-item').attr('checked',false)
            }
            _filter()
        })
        $('.brand-item').change(function(){
            _filter()
        })
        $('#login-button').click(function(){
            uni_modal("","registration.php")
        })
    })
</script>