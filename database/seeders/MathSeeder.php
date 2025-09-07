<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;
class MathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subject = Subject::firstOrCreate(['name' => 'Toán']);


        Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tính giá trị của tích phân I_5 = \\int_0^{\\frac{\\pi}{2}} \\sin^5 x\\, dx',
            'type_id' => 3,
            'formula_latex' => '\\int_0^{\\frac{\\pi}{2}} \\sin^5 x\\, dx',
            'correct_answer' => '\\frac{8}{15}',
            'created_by' => 1,
        ]);


        $q2 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tìm phương trình đường cong (C) luôn tiếp xúc với (C_m): y = x^3 - 2x^2 + mx + \\frac{1 - m^2}{4}',
            'type_id' => 1,
            'formula_latex' => 'y = x^3 - 2x^2 + mx + \\frac{1 - m^2}{4}',
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q2->id, 'content' => 'y = x^3 - x^2 + \\frac{1}{4}', 'is_correct' => true],
            ['question_id' => $q2->id, 'content' => 'y = x^3 - x^2 + 1', 'is_correct' => false],
            ['question_id' => $q2->id, 'content' => 'y = x^3 - x^2 - \\frac{1}{4}', 'is_correct' => false],
            ['question_id' => $q2->id, 'content' => 'y = x^3 - x^2 - 1', 'is_correct' => false],
        ]);

        $q3 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Phương trình tiếp tuyến đồ thị y = \\frac{\\ln x + 2}{\\ln x - 1} tại x = 1 là:',
            'type_id' => 1,
            'formula_latex' => 'y = \\frac{\\ln x + 2}{\\ln x - 1}',
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q3->id, 'content' => 'y = 3x - 1', 'is_correct' => false],
            ['question_id' => $q3->id, 'content' => 'y = -3x + 1', 'is_correct' => true],
            ['question_id' => $q3->id, 'content' => 'y = -3x + 3', 'is_correct' => false],
            ['question_id' => $q3->id, 'content' => 'y = 3x + 1', 'is_correct' => false],
        ]);
         $q4 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho hàm số f(x) = a\sin x + b\cos x + 1. Để f\'(0)=\dots và f(-\frac{\pi}{4}) = 1, thì giá trị a, b bằng bao nhiêu?',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q4->id, 'content' => 'a = b = \sqrt{2} - 2', 'is_correct' => false],
            ['question_id' => $q4->id, 'content' => 'a = \frac{1}{2}, b = -1', 'is_correct' => false],
            ['question_id' => $q4->id, 'content' => 'a = \frac{1}{2}, b = 1', 'is_correct' => false],
            ['question_id' => $q4->id, 'content' => 'a = b = 2', 'is_correct' => true],
        ]);

        $q5 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Khoảng cách giữa hai điểm cực đại và cực tiểu của đồ thị hàm số y = (x+1)(x-2)^2 là:',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q5->id, 'content' => '2\sqrt{5}', 'is_correct' => true],
            ['question_id' => $q5->id, 'content' => '2', 'is_correct' => false],
            ['question_id' => $q5->id, 'content' => '4\sqrt{}', 'is_correct' => false],
            ['question_id' => $q5->id, 'content' => '\sqrt{5}\cdot 2', 'is_correct' => false],
        ]);

        $q6 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Trong không gian Oxyz, cho mặt cầu (S): x^2 + y^2 + z^2 - 2x + 4y - 6z - 11 = 0 và mặt phẳng (P): 2x+2y-z+17=0. Viết phương trình mặt phẳng (Q) song song với (P) và cắt (S) tạo giao tuyến là đường tròn có chu vi bằng 6\pi.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q6->id, 'content' => '(Q): 2x + 2y - z = 0', 'is_correct' => false],
            ['question_id' => $q6->id, 'content' => '(Q): 2x + 2y - z + 5 = 0', 'is_correct' => false],
            ['question_id' => $q6->id, 'content' => '(Q): 2x + 2y - z + 2 = 0', 'is_correct' => false],
            ['question_id' => $q6->id, 'content' => '(Q): 2x + 2y - z - 7 = 0', 'is_correct' => true],
        ]);
        $q7 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Hàm số f(x)= trên đoạn [2;4] có giá trị lớn nhất và nhỏ nhất theo thứ tự là: x+1 7',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q7->id, 'content' => '3 1 5', 'is_correct' => true],
            ['question_id' => $q7->id, 'content' => '1 3 1', 'is_correct' => false],
            ['question_id' => $q7->id, 'content' => '1 2 2', 'is_correct' => false],
            ['question_id' => $q7->id, 'content' => 'Kết quả khác', 'is_correct' => false],
        ]);

        $q9 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho hình chóp S.ABCD có SA⊥(ABCD), ABCD là hình chữ nhật. SA=AD =2a. Góc giữa (SBC) và mặt đáy (ABCD) là 60o. Gọi G là trọng tâm của tam giác SBC. Tính thể tích khối chóp S.AGD. √ 32a3 3',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q9->id, 'content' => '27√ 8a3 3', 'is_correct' => false],
            ['question_id' => $q9->id, 'content' => '27√ 4a3 3', 'is_correct' => true],
            ['question_id' => $q9->id, 'content' => '9 16a3', 'is_correct' => false],
            ['question_id' => $q9->id, 'content' => '√ 9 3', 'is_correct' => false],
        ]);

        $q10 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Đường cong (C):y =x63+2mx2-4x-8m luôn đi qua 2 điểm nguyên cố định nào với mọi m?',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q10->id, 'content' => 'I(0;2) và I(0;-2)', 'is_correct' => false],
            ['question_id' => $q10->id, 'content' => 'I(2;0) và I(-2;0)', 'is_correct' => true],
            ['question_id' => $q10->id, 'content' => 'I(1;2) và I(1;-2)', 'is_correct' => false],
            ['question_id' => $q10->id, 'content' => 'I(2;1) và I(-2;1)', 'is_correct' => false],
        ]);

        $q11 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Hàm số y =cos3xcosx là một hàm tuần hoàn có chu kì là: π',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q11->id, 'content' => 'T = 3 π', 'is_correct' => false],
            ['question_id' => $q11->id, 'content' => 'T = 4 π', 'is_correct' => false],
            ['question_id' => $q11->id, 'content' => 'T = 2', 'is_correct' => false],
            ['question_id' => $q11->id, 'content' => 'T =π x3', 'is_correct' => true],
        ]);

        $q12 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tìm a để hàm số y = (a-1)x2+(a+3)x đồng biến trên khoảng (0;3) 3',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q12->id, 'content' => 'a≥3 10', 'is_correct' => false],
            ['question_id' => $q12->id, 'content' => 'a≥ 7 12', 'is_correct' => false],
            ['question_id' => $q12->id, 'content' => 'a≥ 7 15', 'is_correct' => true],
            ['question_id' => $q12->id, 'content' => 'a≥ 7', 'is_correct' => false],
        ]);
        $q13 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho hàm số y = x^3-3x^2+mx+1 và (d): y = x+1. Tìm tất cả các giá trị của tham số m để đồ thị hàm số cắt (d) tại ba điểm phân biệt có hoành độ x1, x2, x3 thoả mãn x1^2 + x2^2 + x3^2 ≤ 1.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q13->id, 'content' => 'm \geq 5', 'is_correct' => false],
            ['question_id' => $q13->id, 'content' => '0 \leq m \leq 5', 'is_correct' => false],
            ['question_id' => $q13->id, 'content' => '\text{Không tồn tại } m', 'is_correct' => true],
            ['question_id' => $q13->id, 'content' => '5 \leq m \leq 10', 'is_correct' => false],
        ]);

        $q14 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Quả bóng đá được dùng thi đấu tại các giải bóng đá Việt Nam có chu vi thiết diện qua tâm là 68.5 cm. Quả bóng gồm các miếng da hình lục giác đều mỗi miếng diện tích 49.83 cm². Hỏi cần ít nhất bao nhiêu miếng da?',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q14->id, 'content' => '40', 'is_correct' => false],
            ['question_id' => $q14->id, 'content' => '20', 'is_correct' => false],
            ['question_id' => $q14->id, 'content' => '35', 'is_correct' => false],
            ['question_id' => $q14->id, 'content' => '30', 'is_correct' => true],
        ]);

        $q15 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho hàm số f(x) = \\begin{cases} x^2 + xy + y^2 = 4 \\\\ 1 + \\cos x, \\text{ khi } \\cos x < 0 \\end{cases}. Hỏi hàm số f(x) có bao nhiêu điểm gián đoạn trên khoảng (0; 2018)?',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q15->id, 'content' => '2018', 'is_correct' => false],
            ['question_id' => $q15->id, 'content' => '321', 'is_correct' => true],
            ['question_id' => $q15->id, 'content' => '542', 'is_correct' => false],
            ['question_id' => $q15->id, 'content' => '1009', 'is_correct' => false],
        ]);

        $q16 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Giải hệ phương trình: \\begin{cases} x + y + xy = 2 \\end{cases}',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q16->id, 'content' => '(0;2) và (2;0)', 'is_correct' => true],
            ['question_id' => $q16->id, 'content' => '(1;2) và (2;1)', 'is_correct' => false],
            ['question_id' => $q16->id, 'content' => '(-1;-2) và (-2;-1)', 'is_correct' => false],
            ['question_id' => $q16->id, 'content' => 'Kết quả khác', 'is_correct' => false],
        ]);

        $q17 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho đường tròn (C): x^2 + (y-2)^2 = 25 và đường thẳng (d): 3x + 4y + m = 0. (d) cắt (C) tại hai điểm A, B sao cho AB = 8. Tìm giá trị m.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q17->id, 'content' => 'm = -8 hoặc m = 7', 'is_correct' => false],
            ['question_id' => $q17->id, 'content' => 'm = -23 hoặc m = -10', 'is_correct' => true],
            ['question_id' => $q17->id, 'content' => 'm = 8 hoặc m = -7', 'is_correct' => false],
            ['question_id' => $q17->id, 'content' => 'm = 23', 'is_correct' => false],
        ]);
        $q18 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Giải phương trình \sin^2(3x) - \cos^2(4x) = \sin^2(5x) - \cos^2(6x).',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q18->id, 'content' => 'x = \frac{k\pi}{9} hoặc x = \frac{k\pi}{4}', 'is_correct' => false],
            ['question_id' => $q18->id, 'content' => 'x = \frac{k\pi}{5} hoặc x = \frac{k\pi}{4}', 'is_correct' => false],
            ['question_id' => $q18->id, 'content' => 'x = \frac{k\pi}{5} hoặc x = \frac{k\pi}{2}', 'is_correct' => false],
            ['question_id' => $q18->id, 'content' => 'x = \frac{k\pi}{9} hoặc x = \frac{k\pi}{2}', 'is_correct' => true],
        ]);

        $q19 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tính tích phân I = \int_{0}^{\frac{\pi}{2}} \sin^5 x \, dx.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q19->id, 'content' => '\frac{8}{15}', 'is_correct' => true],
            ['question_id' => $q19->id, 'content' => '\frac{4}{15}', 'is_correct' => false],
            ['question_id' => $q19->id, 'content' => '\frac{2}{5}', 'is_correct' => false],
            ['question_id' => $q19->id, 'content' => '\frac{16}{15}', 'is_correct' => false],
        ]);

        $q20 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho f(x) = \int_{0}^{x} \frac{t^2}{1 + t^4} dt. Khẳng định nào sau đây đúng?',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q20->id, 'content' => 'f(x) đồng biến trên \mathbb{R}', 'is_correct' => true],
            ['question_id' => $q20->id, 'content' => 'f(x) có cực tiểu tại x = 0', 'is_correct' => false],
            ['question_id' => $q20->id, 'content' => 'f(x) có cực đại tại x = 0', 'is_correct' => false],
            ['question_id' => $q20->id, 'content' => 'f(x) giảm trên \mathbb{R}', 'is_correct' => false],
        ]);

        $q21 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tìm nguyên hàm của f(x) = \frac{\ln x}{x}.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q21->id, 'content' => '$F(x) = \frac{(\ln x)^2}{2} + C$', 'is_correct' => true],
            ['question_id' => $q21->id, 'content' => '$F(x) = \frac{1}{2\ln x} + C$', 'is_correct' => false],
            ['question_id' => $q21->id, 'content' => '$F(x) = \frac{1}{x\ln x} + C$', 'is_correct' => false],
            ['question_id' => $q21->id, 'content' => '$F(x) = \ln^2 x + C$', 'is_correct' => false],
        ]);

        $q22 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tính giới hạn $\lim_{x \to 0} \frac{\sin(3x)}{x}$.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q22->id, 'content' => '$1$', 'is_correct' => false],
            ['question_id' => $q22->id, 'content' => '$3$', 'is_correct' => true],
            ['question_id' => $q22->id, 'content' => '$0$', 'is_correct' => false],
            ['question_id' => $q22->id, 'content' => '$\infty$', 'is_correct' => false],
        ]);
        $q23 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Cho hình chóp $S.ABCD$ có đáy $ABCD$ là hình vuông cạnh $a$, cạnh bên $SA$ vuông góc với đáy và $SA = a\sqrt{2}$. Thể tích khối chóp $S.ABCD$ là:',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q23->id, 'content' => '$V = \frac{a^3\sqrt{2}}{3}$', 'is_correct' => true],
            ['question_id' => $q23->id, 'content' => '$V = a^3$', 'is_correct' => false],
            ['question_id' => $q23->id, 'content' => '$V = \frac{a^3}{2}$', 'is_correct' => false],
            ['question_id' => $q23->id, 'content' => '$V = \frac{a^3}{3}$', 'is_correct' => false],
        ]);

        $q24 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Trong không gian $Oxyz$, gọi $H$ là hình chiếu vuông góc của điểm $M(5;1;6)$ lên đường thẳng $(d): \frac{x+1}{2} = \frac{y}{3} = \frac{z-2}{1}$. Tọa độ điểm $H$ là:',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q24->id, 'content' => '$(1;0;-2)$', 'is_correct' => false],
            ['question_id' => $q24->id, 'content' => '$(-1;-2;0)$', 'is_correct' => false],
            ['question_id' => $q24->id, 'content' => '$(1;-2;4)$', 'is_correct' => false],
            ['question_id' => $q24->id, 'content' => '$(1;2;4)$', 'is_correct' => true],
        ]);

        $q25 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Hàm số $y = x^3 - 3x^2 + 2$ có đồ thị:',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q25->id, 'content' => 'Có 1 cực đại và 1 cực tiểu', 'is_correct' => true],
            ['question_id' => $q25->id, 'content' => 'Không có cực trị', 'is_correct' => false],
            ['question_id' => $q25->id, 'content' => 'Có cực đại tại $x = 2$', 'is_correct' => false],
            ['question_id' => $q25->id, 'content' => 'Có cực tiểu tại $x = 0$', 'is_correct' => false],
        ]);

        $q26 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tính tích phân $I = \int_0^1 x^2 e^x \, dx$:',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q26->id, 'content' => '$I = (1 - 2 + 2)e^1 - 0 = e$', 'is_correct' => true],
            ['question_id' => $q26->id, 'content' => '$I = e^2 - 1$', 'is_correct' => false],
            ['question_id' => $q26->id, 'content' => '$I = \frac{e^1 - 1}{2}$', 'is_correct' => false],
            ['question_id' => $q26->id, 'content' => '$I = 1$', 'is_correct' => false],
        ]);

        $q27 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Tìm giới hạn $\lim_{x \to 0} \frac{1 - \cos x}{x^2}$.',
            'type_id' => 1,
            'created_by' => 1,
        ]);
        Answer::insert([
            ['question_id' => $q27->id, 'content' => '$\frac{1}{2}$', 'is_correct' => true],
            ['question_id' => $q27->id, 'content' => '$0$', 'is_correct' => false],
            ['question_id' => $q27->id, 'content' => '$1$', 'is_correct' => false],
            ['question_id' => $q27->id, 'content' => '$\infty$', 'is_correct' => false],
        ]);

        $q51 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Điền số tự nhiên vào chỗ trống. Mô đun của số phức $(34i)^2$ là: ____',
            'type_id' => 2,
            'correct_answer' => '1156',
            'created_by' => 1,
        ]);


        $q52 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Trong tập số phức, cho cấp số nhân $(u_n)$ có $u_1 = 1$ và công bội $q = 1 + i$. Điền “Đ” hoặc “S” vào ô trống bên cạnh mỗi phát biểu:\n- Số hạng $u_3$ là số thuần ảo: ____\n- Số hạng $u_4$ là số thực: ____',
            'type_id' => 2,
            'correct_answer' => 'S, Đ',
            'created_by' => 1,
        ]);


        $q53 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Yến có 20 cuốn sách khác nhau và định chia đều số sách vào 2 chiếc thùng giấy để chở tới trường.\n- Số cách Yến có thể xếp sách vào 2 thùng có màu sắc khác nhau là: ____\n- Số cách Yến có thể xếp sách vào 2 thùng giống hệt nhau là: ____',
            'type_id' => 2,
            'correct_answer' => '2^19, \\frac{1}{2}(2^{20} + \\binom{20}{10})',
            'created_by' => 1,
        ]);

        $q104 = Question::create([
            'subject_id' => $subject->id,
            'content' => 'Ở hình vẽ dưới, miền đa giác thu được khi lấy hình $(H)$ hợp với ảnh đối xứng của nó qua trục $AB$ có diện tích bằng bao nhiêu ô vuông?',
            'image_url' => 'storage/questions/q104_hinh38.png',
            'type_id' => 1,
            'created_by' => 1,
        ]);

        Answer::insert([
            ['question_id' => $q104->id, 'content' => '35', 'is_correct' => false],
            ['question_id' => $q104->id, 'content' => '50', 'is_correct' => true],
            ['question_id' => $q104->id, 'content' => '44', 'is_correct' => false],
            ['question_id' => $q104->id, 'content' => '25', 'is_correct' => false],
        ]);

    }
}
