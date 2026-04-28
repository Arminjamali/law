<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'حقوق مدنی', 'color' => '#4f46e5', 'order' => 1,
                'topics' => ['کلیات','اشخاص','اموال','مالکیت','عقد','شروط ضمن عقد','بیع','اجاره','عاریه','قرض','وکالت','ودیعه','هبه','صلح','ضمان قهری','ارث','وصیت','نکاح','طلاق'],
                'resources' => [
                    ['name' => 'حقوق مدنی کاتوزیان', 'type' => 'book', 'author' => 'کاتوزیان'],
                    ['name' => 'جزوه حقوق مدنی هلی', 'type' => 'pamphlet', 'author' => 'موسسه هلی'],
                ],
            ],
            [
                'name' => 'حقوق جزا', 'color' => '#dc2626', 'order' => 2,
                'topics' => ['کلیات جزا','عناصر جرم','مسئولیت کیفری','حدود','قصاص','دیات','تعزیرات','جرایم علیه اشخاص','جرایم علیه اموال','جرایم علیه آسایش عمومی'],
                'resources' => [
                    ['name' => 'حقوق جزای عمومی اردبیلی', 'type' => 'book', 'author' => 'اردبیلی'],
                    ['name' => 'جزوه جزا', 'type' => 'pamphlet', 'author' => ''],
                ],
            ],
            [
                'name' => 'آیین دادرسی مدنی', 'color' => '#0891b2', 'order' => 3,
                'topics' => ['کلیات','صلاحیت','دادخواست','جلسه دادرسی','ادله اثبات دعوا','رأی','تجدیدنظر','فرجام','اعاده دادرسی','اجرای احکام'],
                'resources' => [
                    ['name' => 'آدم شمس', 'type' => 'book', 'author' => 'شمس'],
                    ['name' => 'جزوه آدم', 'type' => 'pamphlet', 'author' => ''],
                ],
            ],
            [
                'name' => 'آیین دادرسی کیفری', 'color' => '#7c3aed', 'order' => 4,
                'topics' => ['کلیات','کشف جرم','بازجویی','بازپرسی','قرارها','کیفرخواست','دادگاه کیفری','تجدیدنظر کیفری','اجرای احکام کیفری'],
                'resources' => [
                    ['name' => 'آدک خالقی', 'type' => 'book', 'author' => 'خالقی'],
                ],
            ],
            [
                'name' => 'حقوق تجارت', 'color' => '#059669', 'order' => 5,
                'topics' => ['تاجر','دفاتر تجاری','شرکت‌های تجاری','شرکت سهامی','شرکت با مسئولیت محدود','اسناد تجاری','برات','سفته','چک','ورشکستگی'],
                'resources' => [
                    ['name' => 'حقوق تجارت اسکینی', 'type' => 'book', 'author' => 'اسکینی'],
                    ['name' => 'جزوه تجارت', 'type' => 'pamphlet', 'author' => ''],
                ],
            ],
            [
                'name' => 'اصول فقه', 'color' => '#d97706', 'order' => 6,
                'topics' => ['مقدمه','وضع','دلالت','اوامر','نواهی','عام و خاص','مطلق و مقید','مجمل و مبین','حجج','اجتهاد و تقلید'],
                'resources' => [
                    ['name' => 'اصول فقه مظفر', 'type' => 'book', 'author' => 'مظفر'],
                ],
            ],
            [
                'name' => 'حقوق اساسی', 'color' => '#be185d', 'order' => 7,
                'topics' => ['کلیات','حاکمیت','قوه مقننه','قوه مجریه','قوه قضاییه','حقوق ملت','نهادهای انتخاباتی'],
                'resources' => [
                    ['name' => 'حقوق اساسی هاشمی', 'type' => 'book', 'author' => 'هاشمی'],
                ],
            ],
            [
                'name' => 'حقوق اداری', 'color' => '#0f766e', 'order' => 8,
                'topics' => ['کلیات','سازمان اداری','اعمال اداری','مسئولیت دولت','دیوان عدالت اداری'],
                'resources' => [
                    ['name' => 'حقوق اداری طباطبایی موتمنی', 'type' => 'book', 'author' => 'طباطبایی موتمنی'],
                ],
            ],
        ];

        foreach ($subjects as $i => $data) {
            $subject = Subject::create([
                'name'  => $data['name'],
                'color' => $data['color'],
                'order' => $data['order'],
            ]);

            foreach ($data['topics'] as $order => $topicName) {
                Topic::create([
                    'subject_id' => $subject->id,
                    'name'       => $topicName,
                    'difficulty' => 3,
                    'order'      => $order + 1,
                ]);
            }

            foreach ($data['resources'] as $res) {
                Resource::create([
                    'subject_id' => $subject->id,
                    'name'       => $res['name'],
                    'type'       => $res['type'],
                    'author'     => $res['author'] ?: null,
                ]);
            }
        }
    }
}
