@extends('layouts.layoutMaster')

@section('title', '['.$pno.'] '.$asama)

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/form-layouts.js')}}"></script>
@endsection

@section('content')
  <?php
  $pot = $plan[0];

//  $pot = array_merge($pot0, $pot1);


  $asama1 = "";
  $asama1 = $pot->asama1;
  $bd1 = $pot->bd1;
  $den1 = $pot->d1;
  $tu1 = $pot->tu1;
  $goz1 = $pot->g1;
  $iku1 = $pot->iku1;

  $asama2 = $pot->asama2;
  $bd2 = $pot->bd2;
  $den2 = $pot->d2;
  $tu2 = $pot->tu2;
  $goz2 = $pot->g2;
  $iku2 = $pot->iku2;

  $gozetim1 = $pot->gozetim1;
  $gbd1 = $pot->gbd1;
  $gd1 = $pot->gd1;
  $gtu1 = $pot->gtu1;
  $gg1 = $pot->gg1;
  $ikug1 = $pot->ikug1;

  $gozetim2 = $pot->gozetim2;
  $gbd2 = $pot->gbd2;
  $gd2 = $pot->gd2;
  $gtu2 = $pot->gtu2;
  $gg2 = $pot->gg2;
  $ikug2 = $pot->ikug2;

  $ybtar = $pot->ybtar;
  $ybbd = $pot->ybbd;
  $ybd = $pot->ybd;
  $ybtu = $pot->ybtu;
  $ybg = $pot->ybg;
  $ikuyb = $pot->ikuyb;

  $ozeltar = $pot->ozeltar;
  $otbd = $pot->otbd;
  $otd = $pot->otd;
  $ottu = $pot->ottu;
  $otg = $pot->otg;
  $ikuot = $pot->ikuot;

  $asama = trim($pot->asama);
  if ($asama == "") $asama = "BAŞVURU";

  $asama1 = substr($asama1, 1);
  $asama1 = substr($asama1, 0, -1);
  $asama1 = str_replace("|", ", ", $asama1);

  $asama2 = substr($asama2, 1);
  $asama2 = substr($asama2, 0, -1);
  $asama2 = str_replace("|", ", ", $asama2);

  $gozetim1 = substr($gozetim1, 1);
  $gozetim1 = substr($gozetim1, 0, -1);
  $gozetim1 = str_replace("|", ", ", $gozetim1);

  $gozetim2 = substr($gozetim2, 1);
  $gozetim2 = substr($gozetim2, 0, -1);
  $gozetim2 = str_replace("|", ", ", $gozetim2);

  $ozeltar = substr($ozeltar, 1);
  $ozeltar = substr($ozeltar, 0, -1);
  $ozeltar = str_replace("|", ", ", $ozeltar);

  $ea = $pot->eakodu;
  $nace = $pot->nacekodu;
  $kat = str_replace("@", "", $pot->kategori22);
  $oickat = str_replace("ß", "", $pot->kategorioic);
  $enysteknikalan = str_replace("Æ", "", $pot->teknikalanenys);
  $bgkat = str_replace("€", "", $pot->kategoribgys);
  $katnace = "";

  if ($nace != "") $nace = "|" . $nace;
  if ($kat != "") $kat = "@" . str_replace("@", "", $kat);
  $oickat = ($oickat != "") ? "ß" . str_replace("ß", "", $oickat) : str_replace("ß", "", $oickat);
  $enysteknikalan = ($enysteknikalan != "") ? "Æ" . str_replace("Æ", "", $enysteknikalan) : str_replace("Æ", "", $enysteknikalan);
  if ($bgkat != "") $bgkat = "€" . $bgkat;

  echo $katnace = $ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

  $iso900115 = $pot->iso900115varyok == "X" ? true : 0;
  $iso1400115 = $pot->iso1400115varyok == "X" ? true : 0;
  $iso2200018 = $pot->iso2200018varyok == "X" ? true : 0;
  $oicsmiic = $pot->helalvaryok == "X" ? true : 0;
  $oicsmiic6 = $pot->oicsmiik6varyok == "X" ? true : 0;
  $oicsmiic9 = $pot->oicsmiik9varyok == "X" ? true : 0;
  $oicsmiic171 = $pot->oicsmiik171varyok == "X" ? true : 0;
  $oicsmiic24 = $pot->oicsmiik24varyok == "X" ? true : 0;
  $iso45001 = $pot->iso4500118varyok == "X" ? true : 0;
  $iso50001 = $pot->iso5000118varyok == "X" ? true : 0;
  $iso27001 = $pot->iso27001varyok == "X" ? true : 0;

  $kyssistemler = \App\Http\Controllers\Planlama\Plan::getSistemler($pot);
  $oicsistemler = \App\Http\Controllers\Planlama\Plan::getOicSistemler($pot);

  if ($kyssistemler !== "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $kyssistemler . ", " . $oicsistemler;
  }
  if ($kyssistemler === "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $oicsistemler;
  }
  if ($kyssistemler !== "" && $oicsistemler === "") {
    $belgelendirileceksistemler = $kyssistemler;
  }

  $sistemversiyongecis = (isset($pot->sistemversiyongecis)) ? $pot->sistemversiyongecis : "";
  $cevrim = $pot->belgecevrimi;
  $cevrim = ($cevrim == "") ? "1" : $cevrim;

  $kurul0 = mb_substr($pot->firmaadi, 0, 10, 'UTF-8');
  $bitistarihi = date_create_from_format("Y-m-d", $pot->bitistarihi);
  $bitistarihi = ($bitistarihi != "") ? '<a href="http://www.alimentcert.com/belgesorgulama.php?firmaadi=' . $kurul0 . '&belgenumarasi=' . $pot->certno . '" target="_blank">' . date_format($bitistarihi, "d.m.Y") . '</a><br>' : "Sertifika kaydı yok...";
  ?>
  <div class="col-12 text-danger py-3 mb-4">
    <span class="text-muted fw-light">Planlama/</span><h5>[{{$pno}}] {{$pot->firmaadi}}</h5>
    {{$pot->belgelendirmekapsami}}
  </div>
  <!-- Sticky Actions -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div
          class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
          <h5 class="card-title mb-sm-0 me-2">Sticky Action Bar</h5>
          <div class="action-btns">
            <button class="btn btn-outline-primary me-3">
              <span class="align-middle"> Back</span>
            </button>
            <button class="btn btn-primary">
              Place Order
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 mx-auto">
              <!-- 1. Delivery Address -->
              <h5 class="mb-4">1. Delivery Address</h5>
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="fullname" class="form-control" placeholder="John Doe"/>
                    <label for="fullname">Full Name</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input class="form-control" type="text" id="email" name="email" placeholder="john.doe"
                             aria-label="john.doe" aria-describedby="email3"/>
                      <label for="email">Email</label>
                    </div>
                    <span class="input-group-text" id="email3">@example.com</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="phone" class="form-control phone-mask" placeholder="658 799 8941"
                           aria-label="658 799 8941"/>
                    <label for="phone">Contact Number</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="alt-num" class="form-control phone-mask" placeholder="658 799 8941"/>
                    <label for="alt-num">Alternate Number</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating form-floating-outline">
                    <textarea name="address" class="form-control" id="address" rows="2" placeholder="1456, Mall Road"
                              style="height: 65px;"></textarea>
                    <label for="address">Address</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="pincode" class="form-control" placeholder="658468"/>
                    <label for="pincode">Pincode</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="landmark" class="form-control" placeholder="Nr. Wall Street"/>
                    <label for="landmark">Landmark</label>
                  </div>
                </div>
                <div class="col-md">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="city" class="form-control" placeholder="Jackson"/>
                    <label for="city">City</label>
                  </div>
                </div>
                <div class="col-md">
                  <div class="form-floating form-floating-outline">
                    <select id="state" class="select2 form-select" data-allow-clear="true">
                      <option value="">Select</option>
                      <option value="AL">Alabama</option>
                      <option value="AK">Alaska</option>
                      <option value="AZ">Arizona</option>
                      <option value="AR">Arkansas</option>
                      <option value="CA">California</option>
                      <option value="CO">Colorado</option>
                      <option value="CT">Connecticut</option>
                      <option value="DE">Delaware</option>
                      <option value="DC">District Of Columbia</option>
                      <option value="FL">Florida</option>
                      <option value="GA">Georgia</option>
                      <option value="HI">Hawaii</option>
                      <option value="ID">Idaho</option>
                      <option value="IL">Illinois</option>
                      <option value="IN">Indiana</option>
                      <option value="IA">Iowa</option>
                      <option value="KS">Kansas</option>
                      <option value="KY">Kentucky</option>
                      <option value="LA">Louisiana</option>
                      <option value="ME">Maine</option>
                      <option value="MD">Maryland</option>
                      <option value="MA">Massachusetts</option>
                      <option value="MI">Michigan</option>
                      <option value="MN">Minnesota</option>
                      <option value="MS">Mississippi</option>
                      <option value="MO">Missouri</option>
                      <option value="MT">Montana</option>
                      <option value="NE">Nebraska</option>
                      <option value="NV">Nevada</option>
                      <option value="NH">New Hampshire</option>
                      <option value="NJ">New Jersey</option>
                      <option value="NM">New Mexico</option>
                      <option value="NY">New York</option>
                      <option value="NC">North Carolina</option>
                      <option value="ND">North Dakota</option>
                      <option value="OH">Ohio</option>
                      <option value="OK">Oklahoma</option>
                      <option value="OR">Oregon</option>
                      <option value="PA">Pennsylvania</option>
                      <option value="RI">Rhode Island</option>
                      <option value="SC">South Carolina</option>
                      <option value="SD">South Dakota</option>
                      <option value="TN">Tennessee</option>
                      <option value="TX">Texas</option>
                      <option value="UT">Utah</option>
                      <option value="VT">Vermont</option>
                      <option value="VA">Virginia</option>
                      <option value="WA">Washington</option>
                      <option value="WV">West Virginia</option>
                      <option value="WI">Wisconsin</option>
                      <option value="WY">Wyoming</option>
                    </select>
                    <label for="state">State</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="deliveryAdd" checked="">
                    <label class="form-check-label" for="deliveryAdd"> Use this as default delivery address </label>
                  </div>
                </div>

                <label class="form-check-label">Address Type</label>
                <div class="col mt-2">
                  <div class="form-check form-check-inline">
                    <input name="collapsible-address-type" class="form-check-input" type="radio" value=""
                           id="collapsible-address-type-home" checked=""/>
                    <label class="form-check-label" for="collapsible-address-type-home">Home (All day delivery)</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input name="collapsible-address-type" class="form-check-input" type="radio" value=""
                           id="collapsible-address-type-office"/>
                    <label class="form-check-label" for="collapsible-address-type-office"> Office (Delivery between 10
                      AM - 5 PM) </label>
                  </div>
                </div>
              </div>
              <hr>
              <!-- 2. Delivery Type -->
              <h5 class="my-4">2. Delivery Type</h5>
              <div class="row gy-3">
                <div class="col-md">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="customRadioIcon1">
                    <span class="custom-option-body">
                      <i class='mdi mdi-briefcase-account-outline'></i>
                      <span class="custom-option-title"> Standard </span>
                      <small> Delivery in 3-5 days. </small>
                    </span>
                      <input name="customDeliveryRadioIcon" class="form-check-input" type="radio" value=""
                             id="customRadioIcon1" checked/>
                    </label>
                  </div>
                </div>
                <div class="col-md">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="customRadioIcon2">
                    <span class="custom-option-body">
                      <i class='mdi mdi-send-outline'></i>
                      <span class="custom-option-title"> Express </span>
                      <small>Delivery within 2 days.</small>
                    </span>
                      <input name="customDeliveryRadioIcon" class="form-check-input" type="radio" value=""
                             id="customRadioIcon2"/>
                    </label>
                  </div>
                </div>
                <div class="col-md">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="customRadioIcon3">
                    <span class="custom-option-body">
                      <i class='mdi mdi-crown-outline'></i>
                      <span class="custom-option-title"> Overnight </span>
                      <small> Delivery within a days. </small>
                    </span>
                      <input name="customDeliveryRadioIcon" class="form-check-input" type="radio" value=""
                             id="customRadioIcon3"/>
                    </label>
                  </div>
                </div>
              </div>
              <hr>
              <!-- 3. Apply Promo code -->
              <h5 class="my-4">3. Apply Promo code</h5>
              <div class="row g-3">

                <div class="col-sm-10 col-8">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="promo-code" class="form-control" placeholder="TAKEITALL">
                    <label for="promo-code">Promo</label>
                  </div>
                </div>
                <div class="col-sm-2 col-4 d-grid">
                  <button class="btn btn-primary">Apply</button>
                </div>

                <div class="divider divider-dashed">
                  <div class="divider-text">OR</div>
                </div>

                <div class="col-12">
                  <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between flex-column flex-sm-row">
                      <div class="offer">
                        <p class="mb-0 fw-medium">TAKEITALL</p>
                        <span>Apply this code to get 15% discount on orders above 20$.</span>
                      </div>
                      <div class="apply mt-3 mt-sm-0">
                        <button class="btn btn-outline-primary">Apply</button>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between flex-column flex-sm-row">
                      <div class="offer">
                        <p class="mb-0 fw-medium">FESTIVE10</p>
                        <span>Apply this code to get 10% discount on all orders.</span>
                      </div>
                      <div class="apply mt-3 mt-sm-0">
                        <button class="btn btn-outline-primary">Apply</button>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between flex-column flex-sm-row">
                      <div class="offer">
                        <p class="mb-0 fw-medium">MYSTERYDEAL</p>
                        <span>Apply this code to get discount between 10% - 50%.</span>
                      </div>
                      <div class="apply mt-3 mt-sm-0">
                        <button class="btn btn-outline-primary">Apply</button>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              <hr>
              <!-- 4. Payment Method -->
              <h5 class="my-4">4. Payment Method</h5>
              <div class="row g-3">
                <div class="mb-3">
                  <div class="form-check form-check-inline">
                    <input name="collapsible-payment" class="form-check-input form-check-input-payment" type="radio"
                           value="credit-card" id="collapsible-payment-cc" checked=""/>
                    <label class="form-check-label" for="collapsible-payment-cc">
                      Credit/Debit/ATM Card <i class="mdi mdi-card-bulleted-outline"></i>
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input name="collapsible-payment" class="form-check-input form-check-input-payment" type="radio"
                           value="cash" id="collapsible-payment-cash"/>
                    <label class="form-check-label" for="collapsible-payment-cash">
                      Cash On Delivery
                      <i class="mdi mdi-help-circle-outline  text-muted" data-bs-toggle="tooltip"
                         data-bs-placement="top" title="You can pay once you receive the product."></i>
                    </label>
                  </div>
                </div>
                <div id="form-credit-card">
                  <div class="col-12">
                    <div class="mb-3">
                      <label class="form-label w-100" for="creditCardMask">Card Number</label>
                      <div class="input-group input-group-merge">
                        <input type="text" id="creditCardMask" name="creditCardMask"
                               class="form-control credit-card-mask" placeholder="1356 3215 6548 7898"
                               aria-describedby="creditCardMask2"/>
                        <span class="input-group-text cursor-pointer p-1" id="creditCardMask2"><span
                            class="card-type"></span></span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-md-6">
                        <div class="mb-3">
                          <label class="form-label" for="collapsible-payment-name">Name</label>
                          <input type="text" id="collapsible-payment-name" class="form-control" placeholder="John Doe"/>
                        </div>
                      </div>
                      <div class="col-6 col-md-3">
                        <div class="mb-3">
                          <label class="form-label" for="collapsible-payment-expiry-date">Exp. Date</label>
                          <input type="text" id="collapsible-payment-expiry-date" class="form-control expiry-date-mask"
                                 placeholder="MM/YY"/>
                        </div>
                      </div>
                      <div class="col-6 col-md-3">
                        <div class="mb-3">
                          <label class="form-label" for="collapsible-payment-cvv">CVV Code</label>
                          <div class="input-group input-group-merge">
                            <input type="text" id="collapsible-payment-cvv" class="form-control cvv-code-mask"
                                   maxlength="3" placeholder="654"/>
                            <span class="input-group-text cursor-pointer" id="collapsible-payment-cvv2"><i
                                class="bx bx-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Card Verification Value"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Sticky Actions -->
@endsection
