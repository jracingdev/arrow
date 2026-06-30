<nav class="sidebar-nav">

    <ul id="sidebarnav">

        <li><a class="waves-effect waves-dark" href="{!! url('dashboard') !!}" aria-expanded="false">

                <i class="mdi mdi-home"></i>

                <span class="hide-menu">{{trans('lang.dashboard')}}</span>

            </a>
        </li>
        
        <li>
            <a class="waves-effect waves-dark" href="{!! url('services') !!}" aria-expanded="false">

                <i class="mdi mdi-clipboard-text"></i>

                <span class="hide-menu">{{trans('lang.service_plural')}}</span>

            </a>

        </li>
        
        <li>
            <a class="waves-effect waves-dark" href="{!! url('workers') !!}" aria-expanded="false">

                <i class="mdi mdi-account-multiple"></i>

                <span class="hide-menu">{{trans('lang.workers')}}</span>

            </a>

        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! url('bookings') !!}" aria-expanded="false">

                <i class="mdi mdi-cart"></i>

                <span class="hide-menu">{{trans('lang.booking_plural')}}</span>

            </a>

        </li>

          <li>
            <a class="waves-effect waves-dark" href="{!! url('coupons') !!}" aria-expanded="false">

                <i class="mdi mdi-sale"></i>

                <span class="hide-menu">{{trans('lang.coupons')}}</span>

            </a>

        </li>
        
        <li>
            <a class="waves-effect waves-dark" href="{!! url('wallettransaction') !!}" aria-expanded="false">

                <i class="mdi mdi-swap-horizontal"></i>

                <span class="hide-menu">{{trans('lang.wallet_transaction')}}</span>

            </a>

        </li>
        <li>
            <a class="waves-effect waves-dark" href="{!! url('payouts') !!}" aria-expanded="false">

                <i class="mdi mdi-wallet"></i>

                <span class="hide-menu">{{trans('lang.payouts')}}</span>

            </a>

        </li>

    </ul>

    <p class="web_version"></p>

</nav>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="{{ asset('js/geofirestore.js') }}"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>