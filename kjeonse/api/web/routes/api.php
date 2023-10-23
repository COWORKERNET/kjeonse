<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Views\ViewController;


/* ---------------------------------------------------------------------- */
Route::middleware('api')->group(function () {

    // User View APIs.
    Route::prefix('v1')->group(function() {

        //
        Route::post('/sns/{provider}', 'App\Http\Controllers\Users\SocialController@login');
        Route::post('/sns/register/{provider}', 'App\Http\Controllers\Users\SocialController@register');
        Route::post('/sns/{provider}/login', 'App\Http\Controllers\Users\SocialController@login');
        Route::post('/info', 'App\Http\Controllers\Users\JoinController@info');

        Route::get('/import/view', 'App\Http\Controllers\Users\MyPage\MyPageController@import');

        // Join Sending Sms Auth Code & Check
        Route::post('/sms', 'App\Http\Controllers\Users\JoinController@sms');
        Route::post('/check/sms', 'App\Http\Controllers\Users\JoinController@sms_check');

        // request mailing service
        Route::post('/mailing', 'App\Http\Controllers\Users\LoginController@mailing');

        //
        Route::prefix('find')->group(function () {
            Route::post('/password',                    'App\Http\Controllers\Users\MyPage\MyPageController@findPassword');
            Route::post('/password/certificate',        'App\Http\Controllers\Users\MyPage\MyPageController@findPasswordCertificate');
            Route::post('/password/certificate/check',  'App\Http\Controllers\Users\MyPage\MyPageController@findPasswordCertificateCheck');
            Route::post('/password/update',             'App\Http\Controllers\Users\MyPage\MyPageController@findPasswordUpdate');

            Route::post('/account/certificate',         'App\Http\Controllers\Users\MyPage\MyPageController@findAccountSendingSmsAuthCode');
            Route::post('/account/certificate/check',   'App\Http\Controllers\Users\MyPage\MyPageController@findAccountSendingSmsAuthCodeCheck');

        });

        /* ----------------------------------------------------------------------------------------------------------------------- */
        Route::get('/main', [ViewController::class, 'main']);

        /* ----------------------------------------------------------------------------------------------------------------------- */
        Route::prefix('category')->group(function() {
            Route::get('/board/faq',    [ViewController::class, 'category_board_faq']);
        });

        /* ----------------------------------------------------------------------------------------------------------------------- */
        Route::prefix('list')->group(function()     {
            Route::get('/bank',     [ViewController::class, 'bank']);
            Route::get('/guide',    [ViewController::class, 'guide']);
            Route::get('/notice',   [ViewController::class, 'notice']);
            Route::get('/faq',      [ViewController::class, 'faq']);
        });

        /* ----------------------------------------------------------------------------------------------------------------------- */
        Route::prefix('search')->group(function ()  {
            Route::get('/guide',    [ViewController::class, 'search_guide']);
            Route::get('/notice',   [ViewController::class, 'search_notice']);
            Route::get('/faq',      [ViewController::class, 'search_faq']);
        });

        /* ----------------------------------------------------------------------------------------------------------------------- */
        Route::prefix('detail')->group(function ()  {
            Route::put('/guide',    [ViewController::class, 'detail_guide']);
            Route::put('/notice',   [ViewController::class, 'detail_notice']);
        });

        /* ----------------------------------------------------------------------------------------------------------------------- */
        Route::prefix('contest')->group(function()  {
                Route::put('/list',     [ViewController::class, 'contest']);
                Route::put('/detail',   [ViewController::class, 'contest_detail']);
            });

        /* ----------------------------------------------------------------------------------------------------------------------- */

        Route::post('/login',   'App\Http\Controllers\Users\LoginController@login');
        Route::post('/join',    'App\Http\Controllers\Users\JoinController@join');

        Route::middleware('auth:api')->group(function () {

            /* ----------------------------------------------------------------------------------------------------------------------- */
            Route::prefix('auth')->group(function () {

                Route::post('/contest', 'App\Http\Controllers\Users\MyPage\MyPageController@list');

            });

            /* ----------------------------------------------------------------------------------------------------------------------- */
            Route::prefix('dashboard')->group(function () {

                Route::post('/',                    'App\Http\Controllers\Users\Dashboard\IndexController@index');

                Route::post('/stock/info',          'App\Http\Controllers\Users\Stock\StockController@stockMyInfo');
                Route::post('/stock',               'App\Http\Controllers\Users\Stock\StockController@stock');

                Route::prefix('mypage')->group(function () {

                    Route::post('/',                'App\Http\Controllers\Users\MyPage\MyPageController@mypage');
                    Route::post('/estate/check',    'App\Http\Controllers\Users\MyPage\MyPageController@estateCountZeroCheck');

                    Route::post('/estate',              'App\Http\Controllers\Users\MyPage\MyPageController@estateList');
                    Route::post('/contractor',          'App\Http\Controllers\Users\MyPage\MyPageController@estateContractorList');
                    Route::post('/contractor/detail',   'App\Http\Controllers\Users\MyPage\MyPageController@estateContractorDetail');

                    Route::post('/question',        'App\Http\Controllers\Users\MyPage\MyPageController@estateQuestion');
                    Route::post('/question/detail', 'App\Http\Controllers\Users\MyPage\MyPageController@estateQuestionDetail');

                    Route::post('/admin/notice',        'App\Http\Controllers\Users\MyPage\MyPageController@estateAdminNotice');
                    Route::post('/admin/notice/detail', 'App\Http\Controllers\Users\MyPage\MyPageController@estateAdminNoticeDetail');

                    Route::post('/stock/list',      'App\Http\Controllers\Users\Dashboard\IndexController@stockList');
                    Route::post('/dividend',        'App\Http\Controllers\Users\Dashboard\IndexController@dividendList');

                    /* ------------------------------------------------------------------------------------------------------------ */
                    Route::post('/phone/certificate', 'App\Http\Controllers\Users\MyPage\MyPageController@userPhoneUpdateCertificate');
                    Route::post('/phone/certificate/check', 'App\Http\Controllers\Users\MyPage\MyPageController@userPhoneUpdateCertificateCheck');

                    Route::post('/userInfoUpdate',  'App\Http\Controllers\Users\MyPage\MyPageController@infoUpdate');
                    Route::post('/changePassword',  'App\Http\Controllers\Users\MyPage\MyPageController@chagePassword');
                    Route::post('/leave',           'App\Http\Controllers\Users\MyPage\MyPageController@userLeave');
                    Route::post('/update/question', 'App\Http\Controllers\Users\MyPage\MyPageController@questionStatueChange');

                    Route::post('/delete/contractor',   'App\Http\Controllers\Users\MyPage\MyPageController@deleteEstateContractor');
                    Route::post('/delete/question',     'App\Http\Controllers\Users\MyPage\MyPageController@deleteEstateQuestion');

                    Route::post('/update/contractor',   'App\Http\Controllers\Users\MyPage\MyPageController@updateEstateContractor');

                    /* 시설관리 문의 업데이트 API는 기능서 내 포함되지 않아 비활성화 됩니다. */
                    // Route::post('/update/question',     'App\Http\Controllers\Users\MyPage\MyPageController@updateEstateQuestion');

                    Route::post('/register/contractor', 'App\Http\Controllers\Users\MyPage\MyPageController@registerEstateContractor');
                    Route::post('/register/question',   'App\Http\Controllers\Users\MyPage\MyPageController@registerEstateQuestion');

                    Route::post('/register/lessor',     'App\Http\Controllers\Users\MyPage\MyPageController@registerLessor');
                    Route::post('/register/lessee',     'App\Http\Controllers\Users\MyPage\MyPageController@registerLessee');

                    Route::post('/withDrawal',          'App\Http\Controllers\Users\Dashboard\WithDrawalController@requestWithDrawal');

                    Route::post('/register/editor',     'App\Http\Controllers\Users\Dashboard\WithDrawalController@requestWithDrawal');

                    /* ------------------------------------------------------------------------------------------------------------ */
                    Route::prefix('search')->group(function() {
                        Route::post('/stock',       'App\Http\Controllers\Users\MyPage\MyPageController@stockListSearch');
                        Route::post('/estate',       'App\Http\Controllers\Users\MyPage\MyPageController@estateSearch');
                    });

                });

            });

            /* ----------------------------------------------------------------------------------------------------------------------- */
            Route::post('/alarm', 'App\Http\Controllers\Users\MyPage\MyPageController@alarmList');
            Route::post('/alarm/check', 'App\Http\Controllers\Users\MyPage\MyPageController@alarmCheck');

            /* ------------------------------------------------------------------------------------- */
            Route::post('/contest/like',        'App\Http\Controllers\Contest\ContestController@like');
            Route::post('/contest/like/list',   'App\Http\Controllers\Contest\ContestController@likeList');
            Route::post('/category/question',   'App\Http\Controllers\Users\MyPage\MyPageController@category_question');

        });

    });

    // Admin APIs.
    Route::prefix('admin')->group(function() {

        Route::post('/login', 'App\Http\Controllers\Users\LoginController@login');

        Route::middleware('auth:api')->group(function () {

            Route::prefix('list')->group(function () {

                /* ----------------------- */
                Route::post('/popup',       'App\Http\Controllers\Admin\AdminController@list_popup');
                Route::post('/banner',      'App\Http\Controllers\Admin\AdminController@list_banner');
                Route::post('/associate',   'App\Http\Controllers\Admin\AdminController@list_associate');
                Route::post('/introduce',   'App\Http\Controllers\Admin\AdminController@list_introduce');
                Route::post('/guide',       'App\Http\Controllers\Admin\AdminController@list_guide');
                Route::post('/notice',      'App\Http\Controllers\Admin\AdminController@list_notice');
                Route::post('/faq',         'App\Http\Controllers\Admin\AdminController@list_faq');

                /* ----------------------- */
                Route::post('/user',         'App\Http\Controllers\Admin\AdminController@list_user');
                Route::post('/leaved',       'App\Http\Controllers\Admin\AdminController@list_leaved_user');
                Route::post('/contest',      'App\Http\Controllers\Admin\AdminController@list_contest');
                Route::post('/dividend',     'App\Http\Controllers\Admin\AdminController@list_dividend');

                /* ----------------------- */
                Route::post('/estate',       'App\Http\Controllers\Admin\AdminController@list_estate');

                /* ----------------------- */
                Route::post('/assets',       'App\Http\Controllers\Admin\AdminController@list_assets');
                Route::post('/withdraw',     'App\Http\Controllers\Admin\AdminController@list_withdraw');
                Route::post('/deposit',     'App\Http\Controllers\Admin\AdminController@list_deposit');
                /* ----------------------- */

            });


            Route::prefix('detail')->group(function () {

                // [c]
                Route::post('/popup',       'App\Http\Controllers\Admin\AdminController@detail_popup');
                Route::post('/banner',      'App\Http\Controllers\Admin\AdminController@detail_banner');
                Route::post('/associate',   'App\Http\Controllers\Admin\AdminController@detail_associate');
                Route::post('/introduce',   'App\Http\Controllers\Admin\AdminController@detail_introduce');
                Route::post('/guide',       'App\Http\Controllers\Admin\AdminController@detail_guide');
                Route::post('/notice',      'App\Http\Controllers\Admin\AdminController@detail_notice');
                Route::post('/faq',         'App\Http\Controllers\Admin\AdminController@detail_faq');
                /* ----------------------- */

                Route::post('/contest',      'App\Http\Controllers\Admin\AdminController@detail_contest');
                Route::post('/dividend',     'App\Http\Controllers\Admin\AdminController@detail_dividend');
                Route::post('/user',         'App\Http\Controllers\Admin\AdminController@detail_user');

                Route::post('/estate',             'App\Http\Controllers\Admin\AdminController@detail_estate');
                Route::post('/estate/notice',      'App\Http\Controllers\Admin\AdminController@detail_estate_notice');
                Route::post('/estate/contractor',  'App\Http\Controllers\Admin\AdminController@detail_estate_contractor');
                Route::post('/estate/qna',         'App\Http\Controllers\Admin\AdminController@detail_estate_qna');

            });


            Route::prefix('register')->group(function () {
                // [c]
                Route::post('/popup',       'App\Http\Controllers\Admin\AdminController@register_popup');
                Route::post('/banner',      'App\Http\Controllers\Admin\AdminController@register_banner');
                Route::post('/associate',   'App\Http\Controllers\Admin\AdminController@register_associate');
                Route::post('/introduce',   'App\Http\Controllers\Admin\AdminController@register_introduce');
                Route::post('/guide',       'App\Http\Controllers\Admin\AdminController@register_guide');
                Route::post('/notice',      'App\Http\Controllers\Admin\AdminController@register_notice');
                Route::post('/faq',         'App\Http\Controllers\Admin\AdminController@register_faq');
                /* ----------------------- */

                Route::post('/contest',         'App\Http\Controllers\Admin\AdminController@register_contest');
                Route::post('/dividend',        'App\Http\Controllers\Admin\AdminController@register_dividend');
                Route::post('/dividend/user',   'App\Http\Controllers\Admin\AdminController@register_dividend_user');

                /* ----------------------- */
                Route::post('/estate/notice',   'App\Http\Controllers\Admin\AdminController@register_estate_notice');
                Route::post('/estate/contractor',   'App\Http\Controllers\Admin\AdminController@register_estate_contractor');



            });


            Route::prefix('update')->group(function () {

                Route::post('/popup',       'App\Http\Controllers\Admin\AdminController@update_popup');
                Route::post('/banner',      'App\Http\Controllers\Admin\AdminController@update_banner');
                Route::post('/associate',   'App\Http\Controllers\Admin\AdminController@update_associate');
                Route::post('/introduce',   'App\Http\Controllers\Admin\AdminController@update_introduce');
                Route::post('/guide',       'App\Http\Controllers\Admin\AdminController@update_guide');
                Route::post('/notice',      'App\Http\Controllers\Admin\AdminController@update_notice');
                Route::post('/faq',         'App\Http\Controllers\Admin\AdminController@update_faq');
                /* ----------------------- */

                Route::post('/user/leaved', 'App\Http\Controllers\Admin\AdminController@update_user_leaved');
                Route::post('/contest', 'App\Http\Controllers\Admin\AdminController@update_contest');
                Route::post('/estate', 'App\Http\Controllers\Admin\AdminController@update_estate');

                Route::post('/estate/notice', 'App\Http\Controllers\Admin\AdminController@update_estate_notice');
                Route::post('/estate/contractor', 'App\Http\Controllers\Admin\AdminController@update_estate_contractor');
                Route::post('/estate/qna', 'App\Http\Controllers\Admin\AdminController@update_estate_qna');

                /* ----------------------- */
                Route::post('/user/assets', 'App\Http\Controllers\Admin\AdminController@update_user_assets');
                Route::post('/withdraw', 'App\Http\Controllers\Admin\AdminController@update_withdraw');
                Route::post('/deposit', 'App\Http\Controllers\Admin\AdminController@update_deposit');

            });


            Route::prefix('delete')->group(function () {
                Route::post('/popup',       'App\Http\Controllers\Admin\AdminController@delete_popup');
                Route::post('/banner',      'App\Http\Controllers\Admin\AdminController@delete_banner');
                Route::post('/associate',   'App\Http\Controllers\Admin\AdminController@delete_associate');
                Route::post('/introduce',   'App\Http\Controllers\Admin\AdminController@delete_introduce');
                Route::post('/guide',       'App\Http\Controllers\Admin\AdminController@delete_guide');
                Route::post('/notice',      'App\Http\Controllers\Admin\AdminController@delete_notice');
                Route::post('/faq',         'App\Http\Controllers\Admin\AdminController@delete_faq');
                /* ----------------------- */

                Route::post('/contest',         'App\Http\Controllers\Admin\AdminController@delete_contest');
                Route::post('/leaved/user',     'App\Http\Controllers\Admin\AdminController@delete_leaved_user');
                Route::post('/dividend',        'App\Http\Controllers\Admin\AdminController@delete_dividend');
                Route::post('/dividend/user',   'App\Http\Controllers\Admin\AdminController@delete_dividend_user');
                /* ----------------------- */

                Route::post('/estate',              'App\Http\Controllers\Admin\AdminController@delete_estate');
                Route::post('/estate/notice',       'App\Http\Controllers\Admin\AdminController@delete_estate_notice');
                Route::post('/estate/contractor',   'App\Http\Controllers\Admin\AdminController@delete_estate_contractor');
                Route::post('/estate/qna',          'App\Http\Controllers\Admin\AdminController@delete_estate_qna');
                /* ----------------------- */

                Route::post('/withdraw',            'App\Http\Controllers\Admin\AdminController@delete_withdraw');
                Route::post('/deposit',            'App\Http\Controllers\Admin\AdminController@delete_deposit');

            });


            Route::prefix('search')->group(function() {
                Route::post('/user',            'App\Http\Controllers\Admin\AdminController@search_user');
                Route::post('/leaved/user',     'App\Http\Controllers\Admin\AdminController@search_leaved_user');
                Route::post('/contest',         'App\Http\Controllers\Admin\AdminController@search_contest');
                Route::post('/dividend',        'App\Http\Controllers\Admin\AdminController@search_dividend');

                Route::post('/dividend/user',   'App\Http\Controllers\Admin\AdminController@search_dividend_user');
                Route::post('/detail/dividend', 'App\Http\Controllers\Admin\AdminController@search_detail_dividend');

                Route::post('/estate',          'App\Http\Controllers\Admin\AdminController@search_estate');
                Route::post('/user/assets',     'App\Http\Controllers\Admin\AdminController@search_user_assets');
                Route::post('/withdraw',        'App\Http\Controllers\Admin\AdminController@search_withdraw');
                Route::post('/deposit',         'App\Http\Controllers\Admin\AdminController@search_deposit');

                /* ----------------------- */


            });

        });

    });

});
