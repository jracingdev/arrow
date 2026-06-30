@extends('layouts.app')



@section('content')

    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">{{trans('lang.tax')}}</h3>

            </div>

            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                    <li class="breadcrumb-item"><a href="{!! route('tax') !!}">{{trans('lang.tax_plural')}}</a></li>

                    <li class="breadcrumb-item active">{{trans('lang.tax_edit')}}</li>

                </ol>

            </div>



        </div>

        <div class="container-fluid">

            <div class="card pb-4">

                <div class="card-body">



                    <div class="row daes-top-sec mb-3">



                    </div>



                    <div class="error_top"></div>

                    <div class="row vendor_payout_create">

                        <div class="vendor_payout_create-inner">



                            <fieldset>

                                <legend>{{trans('lang.tax_edit')}}</legend>

                                <div class="form-group row width-50">

                                    <label class="col-3 control-label">{{trans('lang.tax_title')}}<span

                                                class="required-field"></span></label>

                                    <div class="col-7">

                                        <input type="text" class="form-control tax_title">

                                        <div class="form-text text-muted">

                                            {{ trans("lang.tax_title_help") }}

                                        </div>

                                    </div>

                                </div>



                                <div class="form-group row width-50">

                                    <label class="col-3 control-label">{{trans('lang.country')}}<span

                                                class="required-field"></span></label>

                                    <div class="col-7">



                                        <?php

                                        $countries = file_get_contents(asset('countriesdata.json'));

                                        $countries = json_decode($countries);

                                        $countries = (array)$countries;

                                        $newcountries = array();

                                        $newcountriesjs = array();

                                        foreach ($countries as $keycountry => $valuecountry) {

                                            $newcountries[$valuecountry->code] = $valuecountry;

                                            $newcountriesjs[$valuecountry->countryName] = $valuecountry->code;

                                        }

                                        ?>



                                        <select name="country" id="country" class="form-control tax_country">

                                            @foreach($countries_data as $country)

                                                <option

                                                        value="{{$country->countryName}}">{{$country->countryName}}

                                                </option>

                                            @endforeach

                                        </select>

                                        <div class="form-text text-muted">

                                            {{ trans("lang.tax_country_help") }}

                                        </div>

                                    </div>

                                </div>



                                <div class="form-group row width-50">

                                    <label class="col-3 control-label">{{trans('lang.tax_type')}}<span

                                                class="required-field"></span></label>

                                    <div class="col-7">

                                        <select class="form-control tax_type">

                                            <option value="fix">

                                                {{trans('lang.fix')}}

                                            </option>

                                            <option value="percentage">

                                                {{trans('lang.percentage')}}

                                            </option>

                                        </select>

                                        <div class="form-text text-muted">

                                            {{ trans("lang.tax_type_help") }}

                                        </div>

                                    </div>

                                </div>



                                <div class="form-group row width-50">

                                    <label class="col-3 control-label">{{trans('lang.tax_amount')}}<span

                                                class="required-field"></span></label>

                                    <div class="col-7">

                                        <input type="number" class="form-control tax_amount" min="0">

                                        <div class="form-text text-muted w-50">

                                            {{ trans("lang.tax_amount_help") }}

                                        </div>

                                    </div>

                                </div>



                                <div class="form-group row width-100">

                                    <label class="col-3 control-label ">{{trans('lang.section')}}</label>

                                    <div class="col-12">

                                        <select name="service_type" id="service_type" class="form-control service_type">

                                            <option value="">{{trans('lang.select_section')}}</option>

                                        </select>

                                    </div>

                                </div>



                                <div class="form-group row width-100">

                                    <div class="form-check">

                                        <input type="checkbox" class="tax_active" id="tax_active">

                                        <label class="col-3 control-label"

                                               for="tax_active">{{trans('lang.enable')}}</label>

                                    </div>

                                </div>

                            </fieldset>



                        </div>

                    </div>

                </div>

                <div class="form-group col-12 text-center btm-btn">

                    <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{

                trans('lang.save')}}

                    </button>

                    <a href="{!! route('tax') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{

                trans('lang.cancel')}}</a>

                </div>

            </div>

        </div>

    </div>



@endsection



@section('scripts')



    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">



    <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>

    <script>



        var id = "<?php echo $id;?>";

        var database = firebase.firestore();

        var ref = database.collection('tax').where("id", "==", id);

        var services = database.collection('sections');



        var append_list = '';



        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';

        var newcountriesjs = JSON.parse(newcountriesjs);



        function formatState(state) {



            if (!state.id) {

                return state.text;

            }

            var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";

            var $state = $(

                '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'

            );

            return $state;

        }



        function formatState2(state) {

            if (!state.id) {

                return state.text;

            }



            var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"

            var $state = $(

                '<span><img class="img-flag" /> <span></span></span>'

            );



            $state.find("span").text(state.text);

            $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");



            return $state;

        }



        $(document).ready(function () {

            $('.tax_menu').addClass('active');



            services.get().then(async function (snapshots) {

                snapshots.docs.forEach((listval) => {

                    var data = listval.data();



                    $('.service_type').append($("<option></option>")

                        .attr("value", data.id)

                        .text(data.name));

                })

            });





            jQuery("#country").select2({

                templateResult: formatState,

                templateSelection: formatState2,

                placeholder: "Select Country",

                allowClear: true

            });



            ref.get().then(async function (snapshots) {

                var data = snapshots.docs[0].data();

                $(".tax_title").val(data.title);

                $(".tax_type").val(data.type);

                $(".tax_country").val(data.country);

                $('.service_type').val(data.sectionId).trigger('change');



                jQuery("#country").select2({

                    templateResult: formatState,

                    templateSelection: formatState2,

                    placeholder: "Select Country",

                    allowClear: true

                });





                $('.tax_amount').val(data.tax);

                if (data.enable) {

                    $('.tax_active').prop('checked', true);

                }

            });



            $(".edit-setting-btn").click(function () {



                var title = $(".tax_title").val();

                var country = $(".tax_country").val();

                var type = $(".tax_type :selected").val();

                var tax = $(".tax_amount").val();

                var sectionId = $('.service_type').val();



                var enable = false;

                if ($(".tax_active").is(':checked')) {

                    enable = true;

                }



                if (title == '') {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.tax_title_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if (tax == '' || tax <= 0) {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.tax_amount_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if (sectionId == '') {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.select_section_error')}}</p>");

                    window.scrollTo(0, 0);

                } else {

                    database.collection('tax').doc(id).update({

                        'title': title,

                        'country': country,

                        'tax': tax,

                        'type': type,

                        'enable': enable,

                        'sectionId': sectionId,

                    }).then(function (result) {

                        window.location.href = '{{ route("tax")}}';

                    });

                }

            })

        })





    </script>

@endsection

