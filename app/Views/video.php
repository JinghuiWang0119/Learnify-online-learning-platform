<!DOCTYPE html>
<html lang="en">

<head>
    <title>Video List</title>
    <style>
        .content-container {
            display: flex;
            justify-content: space-between;
        }

        .video-list-container {
            flex: 1;
            margin-right: 20px;
        }

        #map-container {
            width: 300px;
            height:300px;
        }
    </style>
</head>

<body>
    <div class="content-container">
        <div class="video-list-container">
            <br>
            <h4>Your Course</h4>

            <!-- hide the search result by default -->
            <?php if ($query !== '') { ?>
                <?php if ($totalVideos > 0) { ?>
                    <p>We found <?php echo $totalVideos; ?> video(s) relative to your search "<?php echo $query; ?>"</p>
                <?php } else { ?>
                    <p>No videos found for your search "<?php echo $query; ?>"</p>
                <?php } ?>
            <?php } ?>

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
        </div>
        <div>
            <div id="weather-container">
                <br>
                <h4>Your Current Weather</h4>
                <div id="weather"></div>
            </div>

            <div id="map-container">
                <br>
                <h4>Your Current Location</h4>
                <div id="map" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </div>
    <h4>Add a New Course</h4>
    <p>Warning: This is for authorized user ONLY</p>
    <form method="post" action="<?= base_url('video/add_course') ?>">
        <div class="form-group">
            <label for="id">ID</label>
            <input type="number" class="form-control" name="id" required>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="form-group">
            <label for="filename">Filename</label>
            <input type="text" class="form-control" name="filename">
        </div>
        <div class="form-group">
            <label for="url">URL</label>
            <input type="text" class="form-control" name="url">
        </div>
        <div class="form-group">
            <label for="description1">Description 1</label>
            <textarea class="form-control" name="description1" required></textarea>
        </div>
        <div class="form-group">
            <label for="description2">Description 2</label>
            <textarea class="form-control" name="description2" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY"></script>
    <script>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 15,
                    center: pos
                });
                var marker = new google.maps.Marker({position: pos, map: map});

                fetch('https://api.openweathermap.org/data/2.5/weather?lat=' + pos.lat + '&lon=' + pos.lng + '&appid=0c38f34fe5e2478340a14571ecad62e6')
                    .then(response => response.json())
                    .then(data => {
                        var weatherContainer = document.getElementById('weather');
                        weatherContainer.innerHTML = 'Current Weather: ' + data.weather[0].main + ', Temperature: ' + Math.round(data.main.temp - 273.15) + '°C';
                    })
                    .catch(error => console.error('Error:', error));
            }, function() {
                console.log('Geolocation is not supported by your current browser!');
            });
        } else {
            console.log('Geolocation is not supported by your current browser!');
        }
    </script>

</body>

</html>
