<?php
/* 
Template Name: Silent Auction Template 
*/
acf_form_head();
get_header();
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

<style>
    .acf-field .acf-input>p.description {
        color: #45494c !important;
        display: block !important;
        margin-top: 0.25rem !important;
        font-size: 80% !important;
        font-weight: 400 !important;
    }
</style>

<?php
$auction_items = new WP_Query([
    'post_type' => 'auction-item',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]); ?>

<div class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <?php while ($auction_items->have_posts()) : $auction_items->the_post(); ?>
                <div class="col-4">
                    <div class="bg-white shadow-sm rounded overflow-hidden mb-4">
                        <img class="w-100" src="<?php the_post_thumbnail_url(); ?>" alt="">
                        <div class="px-3 pt-3">
                            <h2 class="h3 p-0 text-body"><?php the_title(); ?></h2>
                            <div class="text-dark">
                                <?php the_field('description'); ?>
                            </div>
                        </div>
                        <div class="px-3 py-2 border-bottom border-top">
                            <?php if (get_field('price')) { ?>
                                <div>
                                    <span class="font-weight-bold">High Bidder</span>
                                </div>
                                <div>
                                    <span class="font-weight-bold mr-2">
                                        $<?php the_field('price'); ?>
                                    </span>
                                    <span class="text-truncate">
                                        <?php the_field('name'); ?>
                                    </span>
                                </div>
                            <?php } else { ?>
                                <div>
                                    <span class="font-weight-bold">Starting Bid</span>
                                </div>
                                <div>
                                    <span class="font-weight-bold mr-2">
                                        $<?php the_field('starting_bid'); ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="p-3">
                            <button type="button" class="btn btn-success btn-block font-weight-bold" data-toggle="modal" data-target="#auctionItemModal" data-item-id="<?php the_ID(); ?>" data-item-title="<?php the_title(); ?>" data-item-image="<?php the_post_thumbnail_url(); ?>" data-item-desc="<?php the_field('description'); ?>" data-item-price="<?php get_field('price') ? the_field('price') : the_field('starting_bid');  ?>" data-bidder-name="<?php the_field('name'); ?>">Select Item</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>


<div class="modal fade" id="auctionItemModal" tabindex="-1" aria-labelledby="auctionItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="auctionItemModalLabel">
                    <span id="itemTitle" class="mr-2">Item Title</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div>
                    <img id="itemImage" class="w-100" src="" alt="">
                </div>
                <div class="px-3 py-2 border-bottom" id="bidText">
                    <div>
                        <span class="font-weight-bold">High Bidder</span>
                    </div>
                    <span class="font-weight-bold">
                        $<span id="itemPrice">
                        </span>
                    </span>
                    <span id="itemBidderName">
                    </span>
                </div>
                <div class="p-3">
                    <?php
                    acf_form([
                        // "form_attributes" => array(
                        //     'method' => 'POST',
                        //     'action' => admin_url("admin-post.php"),
                        // ),
                        "post_id" => -1,
                        'html_updated_message'  => '',
                        'instruction_placement' => 'field',
                        "field_groups" => ["group_5f7a155c8a747"],
                        "fields" => ["field_5f7a1575d3ff7", "field_5f7d26936b718", "field_5f7b23cc208d3"],
                        "html_before_fields" => sprintf('<input type="hidden" name="form_id" value="auctionItemBid">'),
                        'html_submit_button'  => '<input type="submit" class="btn btn-success font-weight-bold" value="Place Bid!" />',
                    ]);
                    ?>
                </div>
            </div>
            <!-- <form>
                <div class="modal-body">
                    <img id="itemImage" class="w-100" src="" alt="">
                    <div id="itemDesc"></div>
                    <div class="form-group">
                        <label for="itemPrice" class="col-form-label">Your Bid:</label>
                        <input type="number" class="form-control" id="itemPrice" step="10"></input>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Place Bid!</button>
                </div>
            </form> -->
        </div>
    </div>
</div>

<script>
    $('#auctionItemModal').on('show.bs.modal', function(event) {
        var emailField = "#acf-field_5f7b23cc208d3"
        var nameField = "#acf-field_5f7d26936b718"
        var bidField = "#acf-field_5f7a1575d3ff7"

        var button = $(event.relatedTarget) // Button that triggered the modal
        var title = button.data('item-title');
        var price = parseInt(button.data('item-price'));
        var desc = button.data('item-desc');
        var image = button.data('item-image');
        var id = button.data('item-id');
        var bidderName = button.data('bidder-name');

        var modal = $(this)
        modal.find('#itemTitle').text(title);
        modal.find('#itemPrice').text(price);
        modal.find('#itemBidderName').text(bidderName);

        if (bidderName === '') {
            modal.find('#bidText').text("Be the Opening Bidder! Starting bid $" + price + ".").addClass("font-weight-bold");
        }

        var newPrice = bidderName === '' ? price : price + 5;

        modal.find(bidField).val(newPrice).prop('required', true).attr('min', newPrice).attr('step', 5);
        // modal.find(bidField);
        // modal.find(bidField);
        modal.find(emailField).val("").prop('required', true);
        modal.find(nameField).val("").prop('required', true);

        modal.find('#itemDesc').html(desc);
        modal.find('#itemImage').attr('src', image);
        modal.find('#_acf_post_id').val(id);
    })
</script>

<?php wp_reset_postdata();

get_footer();
