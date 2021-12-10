<?php

function isFormSubmitted() {
    return isset($_POST['submit']);
}

function apiRequestGET(string $url, string $arg = null, bool $toArr = false) {
    $requestUrl = ($arg == null) ? $url : $url.$arg;
    
    $curl = curl_init($requestUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, $toArr);
}

function getMostProbableNationality(string $name) {
    $nationalizeData = apiRequestGET('https://api.nationalize.io/?name=', $name);

    $ntObb = '';
    $ntProb = 0.0;

    foreach ($nationalizeData->country as $data) {
        if ($ntProb < $data->probability) {
            $ntProb = $data->probability;
            $ntObb = $data->country_id;
        }
    }
    
    return $ntObb;
}

if (isFormSubmitted()) {
    $name = $_POST['name'];
    $ntObb = getMostProbableNationality($name);

    $genderizeData = apiRequestGET('https://api.genderize.io/?name=', $name);
    $apiFirstOrgData = apiRequestGET('https://api.first.org/data/v1/countries?q=', $ntObb, true);
    $agifyData = apiRequestGET('https://api.agify.io/?name=', $name);
    $boredApiData = apiRequestGET('https://www.boredapi.com/api/activity');

    $gender = ($genderizeData->gender) ? $genderizeData->gender : '-';
    $country = ($ntObb != '') ? $apiFirstOrgData['data'][$ntObb]['country'] : '-';
    $age = ($agifyData->age) ? $agifyData->age : '-';
    $activity = ($boredApiData->activity) ? $boredApiData->activity : '-';
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP Project</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </head>
    <body class="bg-light">
        <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">
            <div class="panel p-5 my-5 bg-white shadow rounded-3 position-relative">
                <?php if (!isFormSubmitted()) { ?>
                    <div id="loader" class="visually-hidden container h-100 position-absolute top-0 start-0 bg-white d-flex justify-content-center align-items-center rounded-3" style="z-index: 2;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <h4 class="text-center mb-5 position-relative" style="z-index: 3;">We'll guess about you off of your name</h4>
                    <form action="./" method="POST">
                        <fieldset class="d-flex flex-column justify-content-center align-items-center">
                            <div class="form-floating mb-5">
                                <input type="text" class="form-control disabled" id="floatingInput" name="name" placeholder="First name" pattern="^[^'\x22`]+$" minlength="3" maxlength="32" required>
                                <label for="floatingInput" class="user-select-none">First name</label>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Guess</button>
                        </fieldset>
                    </form>
                <?php } else { ?>
                    <div class="text-center mb-5">
                        <h4 class="fw-bolder mb-5"><?= $name ?></h4>
                        <p class="text-secondary mb-2">Your gender probably is <span class="text-black fw-bolder ms-1"><?= $gender ?></span></p>
                        <p class="text-secondary mb-2">You're probably from <span class="text-black fw-bolder ms-1"><?= $country ?></span></p>
                        <p class="text-secondary mb-5">Your age probably is <span class="text-black fw-bolder ms-1"><?= $age ?></span></p>
                        <p class="text-secondary m-0" style="font-size: 12px;">Are you bored?<span class="text-black ms-1"><?= strtolower($activity) ?></span></p>
                    </div>
                    <div class="container-sm text-center">
                        <a href="./" class="link-primary">try another name</a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if (!isFormSubmitted()) { ?>
            <script src="js/script.js"></script>
        <?php } ?>
    </body>
</html>
