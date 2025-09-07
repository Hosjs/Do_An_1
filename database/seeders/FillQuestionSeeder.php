<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Subject;
class FillQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subject = Subject::firstOrCreate(['name' => 'Văn hóa']);
        $q101 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Văn hoá dân gian (1) cần được bảo tồn và phát huy. Hiện nay, việc bảo tồn và phát huy vốn quý (2) văn hoá vẫn còn nhiều khó khăn và thách thức. Giữ gìn và lan toả bản sắc văn hoá để khẳng định, nâng tầm (3) là trách nhiệm của các địa phương, của các nhà (4) và của mỗi người dân Việt Nam.',
            'type_id' => 4,
            'created_by' => 1,
            'blanks' => 4,
            'choices' => json_encode([
                'di sản',
                'quản lý văn hoá',
                'thời cơ',
                'vị thế dân tộc',
                'bộ gen của văn hoá dân tộc',
                'tinh hoa văn hóa'
            ]),
            'correct_answer' => json_encode([
                'di sản',
                'tinh hoa văn hóa',
                'vị thế dân tộc',
                'quản lý văn hoá'
            ])
        ]);
        $q102 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Công việc của các nhà nghiên cứu, sưu tầm phải đối mặt với (1). Một mặt, di sản văn hoá phi vật thể đang (2) cùng với sự ảnh hưởng của kinh tế thị trường, của tốc độ đô thị và toàn cầu hoá. Mặt khác, lực lượng nghiên cứu, sưu tầm (3) và am hiểu văn hoá dân gian lại ngày càng ít đi.',
            'type_id' => 4,
            'created_by' => 1,
            'blanks' => 3,
            'choices' => json_encode([
                'biến đổi nhanh',
                'dần mai một',
                'nhiều thách thức',
                'tâm huyết',
                'thời cơ mới'
            ]),
            'correct_answer' => json_encode([
                'nhiều thách thức',
                'dần mai một',
                'tâm huyết'
            ])
        ]);
        $q103 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Một chiếc ô tô đang đi trên đường với vận tốc $v(t) = 2t$ ($t \ge 0$) (m/s), trong đó $t$ là khoảng thời gian tính bằng giây. Ô tô bắt đầu xuất phát tại thời điểm $t = 0$. Quãng đường ô tô đi được trong 20 giây là: (1) (m). Khi ô tô chạy được 01 phút thì tài xế đạp phanh, từ thời điểm đó ô tô chuyển động chậm dần với vận tốc $v(t) = -5t + 420$ (m/s). Từ lúc đạp phanh đến khi dừng hẳn ô tô còn di chuyển (2) (m). Tổng quãng đường ô tô đi được (kể từ khi xuất phát đến khi dừng hẳn) là (3) (m).',
            'type_id' => 4,
            'created_by' => 1,
            'blanks' => 3,
            'choices' => json_encode([
                '400',
                '7400',
                '5040',
                '1440',
                '417,5'
            ]),
            'correct_answer' => json_encode([
                '400',
                '1440',
                '5840'
            ])
        ]);


    }
}
