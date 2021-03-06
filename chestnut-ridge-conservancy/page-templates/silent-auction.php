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

    p {
        padding-bottom: 0 !important;
        margin-bottom: .125rem !important;
        font-size: 1rem !important;
    }

    ol {
        list-style: decimal !important;
    }
</style>

<?php
if (isset($_POST['acf'])) {
    print_r($_POST['acf']);
    exit;
}

$auction_items = new WP_Query([
    'post_type' => 'auction-item',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    // 'orderby' => 'title',
    'order' => 'ASC',
]); ?>



<div class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-10 offset-md-1">
                <div class="my-5">
                    <?php the_field('page_content'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            $data = [];
            while ($auction_items->have_posts()) : $auction_items->the_post(); ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="bg-white shadow-sm rounded overflow-hidden mb-4">
                        <img class="w-100" src="<?php the_post_thumbnail_url(); ?>" alt="">
                        <div class="px-3 pt-3">
                            <h2 class="h4 p-0 text-body"><?php the_title(); ?></h2>
                            <div class="text-dark mb-4">
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
                            <?php $rev = wp_get_post_revisions();
                            $bids = [];
                            foreach ($rev as $r => $v) {
                                $item = get_fields($r);
                                $bid_date = new DateTime($v->post_modified);
                                $start_date = new DateTime("10/16/2020");
                                if ($item['name'] && $item['price']) {
                                    $bids[] = [
                                        'id' => $v->ID,
                                        'name' => $item['name'],
                                        'price' => $item['price'],
                                        'email' => $item['email'],
                                        'date' => $v->post_modified,
                                    ];
                                }
                            }

                            $data[] = [
                                'title' => get_the_title(),
                                'bids' => $bids,
                                'name' => get_field('name'),
                                'email' => get_field('email'),
                                'price' => get_field('price')
                            ]

                            ?>

                            <button type="button" class="btn btn-success btn-block font-weight-bold" data-toggle="modal" data-target="#auctionItemModal" data-item-id="<?php the_ID(); ?>" data-item-title="<?php the_title(); ?>" data-item-image="<?php the_post_thumbnail_url(); ?>" data-item-price="<?php get_field('price') ? the_field('price') : the_field('starting_bid');  ?>" data-bidder-name="<?php the_field('name'); ?>" data-item-bids='<?php echo json_encode($bids); ?>'>
                                Click Here to Bid
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <script>
            console.log(<?php echo json_encode($data); ?>)
        </script>
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
                <div class="px-3 py-2 border-bottom" id="noBids"></div>
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
                <div class="px-3 py-2 border-bottom" id="allBids">
                    <button class="btn btn-link shadow-none px-0" type="button" data-toggle="collapse" data-target="#toggleBidList" aria-expanded="false" aria-controls="toggleBidList">
                        Show All Bids
                    </button>
                    <div class="collapse pl-3" id="toggleBidList">
                        <ol id="bidList"></ol>
                    </div>
                </div>
                <div class="p-3">
                    <?php
                    acf_form([
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
        </div>
    </div>
</div>

<div class="modal fade" id="auctionWinnersModal" tabindex="-1" aria-labelledby="auctionWinners" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="auctionWinners">Auction Winners!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="h-auto w-100" src="https://chestnutridgeconservancy.org/wp-content/uploads/2020/11/Winners.jpg" alt="Auction Winners">
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#auctionWinnersModal').modal('show')
    });


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
        var bids = button.data('item-bids');

        var modal = $(this)
        modal.find('#itemTitle').text(title);
        modal.find('#itemPrice').text(price);
        modal.find('#itemBidderName').text(bidderName);

        var bidList = modal.find('#bidList')
        if (bids.length) {
            bids.sort(function(a, b) {
                return Number(a.price) - Number(b.price);
            });
            modal.find('#allBids').removeClass('d-none')
            modal.find('#allBids button').text('Show All Bids (' + bids.length + ')')
            bidList.html('');
            bids.forEach(bid => {
                bidList.append('<li class="mb-1"><span class="font-weight-bold mr-2">$' + bid.price + '</span>' + bid.name + '</li>')
            });
        } else {
            bidList.html('');
            modal.find('#allBids').addClass('d-none')
        }

        if (bidderName === '') {
            modal.find('#noBids').text("Be the Opening Bidder! Starting bid $" + price + ".").addClass("font-weight-bold").removeClass("d-none");
            modal.find('#bidText').addClass("d-none")
        } else {
            modal.find('#noBids').text("").addClass("d-none")
            modal.find('#bidText').removeClass("d-none")
        }

        var newPrice = bidderName === '' ? price : price + 5;

        modal.find(bidField).val(newPrice).prop('required', true).attr('min', newPrice).attr('step', 5);
        modal.find(emailField).val("").prop('required', true);
        modal.find(nameField).val("").prop('required', true);

        modal.find('#itemDesc').html(desc);
        modal.find('#itemImage').attr('src', image);
        modal.find('#_acf_post_id').val(id);
    })
</script>

<?php wp_reset_postdata();

get_footer();
