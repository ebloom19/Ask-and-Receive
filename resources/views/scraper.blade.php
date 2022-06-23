<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Scraper</title>
    <style>
        .propertyCard {
            border-radius: 10px;
            padding: 20px;
            margin: 30px;
            background-color: antiquewhite;
            width: 600px;
        }

        .cards {
            display: flex;
            align-items: center;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div class="cards">
        @foreach ($data as $address => $details)
            <div class="propertyCard">
                <h3>{{ $address }}</h3>
                @foreach ($details['propertyInfo'] as $feature)                    
                    <p>{{ $feature }}</p>
                @endforeach
                <h4>Listing History:</h4>
                @foreach ($details['listingHistory'] as $info)
                    <p>{{ isset($info[1]) ? "{$info[0]} : {$info[1]}" : $info[0] }}</p>        
                @endforeach
            </div>
        @endforeach
    </div>
</body>
</html>