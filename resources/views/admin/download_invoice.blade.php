<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpopo Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

    <style>
        body {
            font-family: sans-serif;
        }

        .heading_title h1 {
            font-size: 25px;
            font-weight: 600;
        }

        .uber_main {
            width: 80%;
            margin: 0 auto;
        }

        .uber_T {
            font-size: 40px;
            color: #000;
            font-weight: 500;
            padding-bottom: 20px;
        }

        table {
            width: 100%;
        }

        .heading_title {
            width: 90%;
            margin: 0 auto;
        }

        .thanks {
            margin-top: 10px;
            font-size: 30px;
            line-height: 1;
            padding-bottom: 20px;
        }

        .hope {
            font-size: 25px;
            padding-bottom: 30px;
            color: #000;
            line-height: 1;
        }

        .price {
            font-size: 30px;
            font-weight: 500;
            padding-bottom: 40px;
        }

        .td_advance {
            font-size: 20px;
            color: #000;
        }

        .down {
            padding-bottom: 10px;
            color: #000;
            font-size: 20px;
        }
        .uber_heading{
            margin-top:40px;
            margin-bottom:80px;
        }
        .trip_result{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .second table tr td{
            float:right;
        }
    </style>

</head>

<body>
    <div class="uber_heading">
        <div class="heading_title">
            <div class="trip_result">
                <div class="first" style="float:left;">
                     <h1>Your Trip With Limpopo Ride </h1>
                  <h5>Booking ID : {{  $get_booking_details->booking_id }}</h5>
                  <h6>Booking Date : {{  date('d-m-Y h:i a', strtotime($get_booking_details->created_at)) }}</h6>
                  <!--<h6>GST Number : 09AALCC0792M1Z3</h6>-->
                 </div>
                  <div class="second" style="float:right;">
                     <img height="100" src="<?= url('public/admin_asstets/img/ride_hailing.png');?>" alt="logo"/>
                  </div>
            </div>
            <h1 style="margin-top:120px;"></h1>
            <hr>

            <table>
                <tr>
                    <td> User : </td>
                    <td>{{  $get_booking_details->user_name }}</td>
                </tr>
                <tr>
                    <td> Driver Name : </td>
                    <td>{{  $get_booking_details->driver_name }}</td>
                </tr>
                <tr>
                    <td> Driver Mobile : </td>
                    <td>{{  $get_booking_details->driver_mobile }}</td>
                </tr>
                <tr>
                    <td> Vehicle : </td>
                    <td>{{  $get_booking_details->vehicle_name }}</td>
                </tr>
                <tr>
                    <td class=""> Pickup Point : </td>
                    <td>{{  $get_booking_details->picup_location }}</td>
                </tr>
                <tr>
                    <td> Drop : </td>
                    <td>{{  $get_booking_details->drop_location }}</td>
                </tr>
                <tr>
                    <td> Distance : </td>
                    <td>{{  $get_booking_details->distance }} Km</td>
                </tr>
            </table>
            <hr>
        </div>

        <div class="uber_main container">
            <table>
                <tr>
                    <td class="uber_T">Limpopo Ride</td>
                    <td class="td_advance"><img height="100" src="<?= secure_url('public/vehicle_image/vehicle_type_image/')."/".$get_booking_details->vehicle_image;?>" alt="logo"/></td>
                    <!-- <P>Wed : jun</P> -->


                </tr>

                <tr>
                    <td class="thanks">Thanks for riding  {{  $get_booking_details->vehicle_name }},</td>
                </tr>
                <tr>
                    <td class="hope">We hope you enjoyed your ride</td>
                    <td><img src=""></td>
                </tr>
                
                <tr>
                    <td class="down">Fare</td>
                    <td class="down"> {{  $get_booking_details->fare }} </td>
                </tr>
                
                <tr>
                    <td class="down">Base Fare</td>
                    <td class="down"> {{  $get_booking_details->base_fare }} </td>
                </tr>
                
                <!--<tr>-->
                <!--    <td class="down">Night Charge</td>-->
                <!--    <td class="down"> {{  $get_booking_details->night_charge }} Rupee</td>-->
                <!--</tr>-->
                
                <!--<tr>-->
                <!--    <td class="down">Wating Charge</td>-->
                <!--    <td class="down"> {{  $get_booking_details->wating_charge }} Rupee</td>-->
                <!--</tr>-->
                
                <!-- <tr>-->
                <!--    <td class="down"><h4>Gst ({{ get_option_data('gst_percentage') }} %)</h4></td>-->
                <!--    <td class="down"><h4> {{  ($get_booking_details->gst_amount)??"0.00" }} Rupee</h4></td>-->
                <!--</tr>-->

                <tr>
                    <td class="down"><b>Total Amount</b></td>
                    <td class="down"><b> {{  $get_booking_details->total_fare }} </b></td>
                </tr>

            </table>
        </div>
        <hr style="margin-top:50px">
        <div class="uber_main container" >
            <p>Any Toll, Parking charges and other additional charges will be paid by customers seperately.</p>
        </div>
    </div>

</body>

</html>