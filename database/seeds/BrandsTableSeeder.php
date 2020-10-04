<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            [
                'name_jp' => 'ヨネックス',
                'name_en' => 'YONEX',
                'country' => '日本'
            ],
            [
                'name_jp' => 'スリクソン',
                'name_en' => 'SRIXON',
                'country' => '日本'
            ],
            [
                'name_jp' => 'ウィルソン',
                'name_en' => 'Wilson',
                'country' => 'アメリカ'
            ],
            [
                'name_jp' => 'プリンス',
                'name_en' => 'prince',
                'country' => 'アメリカ'
            ],
            [
                'name_jp' => 'ヘッド',
                'name_en' => 'HEAD',
                'country' => 'アメリカ'
            ],
            [
                'name_jp' => 'バボラ',
                'name_en' => 'Babolat',
                'country' => 'フランス'
            ],
        ];


        DB::table('brands')->insert($param);
    }



}
