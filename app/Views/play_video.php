<!DOCTYPE html>
<html lang="en">

<head>
    <title>Play Video</title>
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            padding-top: 30px;
            height: 0;
            overflow: hidden;
        }

        .video-container iframe,
        .video-container object,
        .video-container embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .video-list-container {
            margin-left: 24px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=AY6XiV2TmdPdx0_F5-TsC73pifFxWEWnVE7SN6JRh4maJj6uQhQaBg4FVWyhCYT__wR3ltTivnURU8pY&locale=en_US"></script>
    <script>
        $(document).ready(function() {
            $("form").submit(function(event) {
                event.preventDefault();

                var content = $("#content").val();
                var video_id = <?= $video['id'] ?>;

                $.ajax({
                    url: "<?= base_url('video/submit_comment') ?>",
                    type: "POST",
                    data: {content: content, video_id: video_id},
                    success: function(response) {
                        $("#content").val('');
                        loadComments();
                    }
                });
            });

            var isLoading = false;

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    if (!isLoading) {
                        isLoading = true;
                        loadComments();
                    }
                }
            });

            loadComments();
            loadLikes();

            $("#like-button").click(function() {
                likeVideo();
            });


            $(function() {
                initializePayPalButton();
            });

        });

        function loadComments() 
        {
            var video_id = <?= $video['id'] ?>;

            $.ajax({
                url: "<?= base_url('video/get_comments') ?>",
                type: "GET",
                data: {video_id: video_id},
                success: function(response) {
                    var comments = JSON.parse(response);
                    renderComments(comments);
                    isLoading = false;
                }
            });
        }

        function renderComments(comments) 
        {
            var commentsDiv = $(".comments");
            commentsDiv.empty();

            if (comments.length == 0) {
                commentsDiv.append('<p>No comments yet. Be the first to comment!</p>');
            } else {
                comments.forEach(function(comment) {
                    var commentHtml = `
                        <div id="comment-${comment.id}" class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">${comment.username}</h5>
                                <p class="card-text">${comment.content}</p>
                                <p class="card-text">
                                    <small class="text-muted">${comment.created_at}</small>
                                </p>
                            </div>
                        </div>`;
                    commentsDiv.append(commentHtml);
                });
            }
        }

        function loadLikes() {
            var video_id = <?= $video['id'] ?>;

            $.ajax({
                url: "<?= base_url('video/get_likes') ?>",
                type: "GET",
                data: {video_id: video_id},
                success: function(response) {
                    var likeCount = JSON.parse(response);
                    $("#like-count").text(likeCount);
                }
            });
        }

        function likeVideo() {
            var video_id = <?= $video['id'] ?>;

            $.ajax({
                url: "<?= base_url('video/like_video') ?>",
                type: "POST",
                data: {video_id: video_id},
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        loadLikes();
                    } else {
                        alert(result.error);
                    }
                }
            });
        }

        function initializePayPalButton() {
            paypal.Buttons({
                style: {
                    shape: 'rect',
                    color: 'gold',
                    size: 'medium',
                    layout: 'horizontal',
                    label: 'buynow',
                },
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '5.00'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('Transaction completed by ' + details.payer.name.given_name + '!');
                    });
                },
                onError: function(err) {
                    console.error('Error during the transaction:', err);
                }
            }).render('#paypal-button-container');
        }


    </script>

</head>

<body>
    <div class="container">
        <br>
        <h2 class="text-center"><?= $video['title'] ?></h2>
        <br>
        <div class="row">
            <div class="col-lg-8">
                <div class="video-container">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $video['url'] ?>?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <br>
                <p id="video-description"><?= $video['description1'] ?></p>
                <p id="video-description"><?= $video['description2'] ?></p>
                <div class="mt-3">
                    <button id="like-button" class="btn btn-primary" <?php if (!session()->has('user_id')): ?>disabled<?php endif; ?>>Like</button>
                    <span id="like-count">0</span> Likes
                </div>
                <h3 class="mt-4">Comments</h3>
                <?php if (session()->has('user_id')): ?>
                    <form>
                        <div class="form-group">
                            <label for="content">Leave a comment:</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                <?php else: ?>
                    <p>Please <a href="<?= base_url('login') ?>">log in</a> to leave a comment.</p>
                <?php endif; ?>

                <div class="mt-4 comments">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="video-list-container">
                    <h4>Collection</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos as $video_item): ?>
                            <tr>
                                <td><?= $video_item['id'] ?></td>
                                <td><?= $video_item['title'] ?></td>
                                <td>
                                    <a href="<?= base_url('video/play/' . $video_item['id']) ?>" class="btn btn-primary">Play</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p>Buy this video and enjoy the journey of becoming a Unity master.</p>
                    <div id="paypal-button-container"></div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>