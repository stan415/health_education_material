<?php


function check(string $str)
{
    $stack = [];

    for($i = 0; $i < strlen($str); ++$i)
    {
        switch($str[$i])
        {
            case '{':
            case '[':
            case '<':
            case '(':
                $stack[] = $str[$i]; // 是左边的就入栈
                break;
            case '}':
            case ']':
            case '>':
            case ')':
                $left = array_pop($stack); // 是右边的就出栈
                if (!ismatch($left, $str[$i])) // 然后检查是否配对
                    return false;
                break;
        }
    }
    return empty($stack); // 最后看堆栈是否为空，以免全部都是加的左括号
}

function ismatch(string $left, string $right) {
    $delta = ord($right) - ord($left);
    return $left == '(' ? $delta == 1 : $delta == 2; // ASCII 表中 除了 () 相减是1外, 其它的都是 2
}

//var_dump(check('{([()(])<()>)}')); // false



/**
 * =================================================================
 *                          责任链模式
 * =================================================================
 */
/*
// 词汇过滤链条
abstract class FilterChain
{
    protected $next;
    public function setNext($next)
    {
        $this->next = $next;
    }
    abstract public function filter($message);
}

// 严禁词汇
class FilterStrict extends FilterChain
{
    public function filter($message)
    {
        foreach (['枪X', '弹X', '毒X'] as $v) {
            if (strpos($message, $v) !== false) {
                var_dump('该信息包含敏感词汇！');
                die;
            }
        }
        if ($this->next) {
            return $this->next->filter($message);
        } else {
            return $message;
        }
    }
}

// 警告词汇
class FilterWarning extends FilterChain
{
    public function filter($message)
    {
        $message = str_replace(['打架', '丰胸', '偷税'], '*', $message);
        if ($this->next) {
            return $this->next->filter($message);
        } else {
            return $message;
        }
    }
}

// 手机号加星
class FilterMobile extends FilterChain
{
    public function filter($message)
    {
        $message = preg_replace("/(1[3|5|7|8]\d)\d{4}(\d{4})/i", "$1****$2", $message);
        if ($this->next) {
            return $this->next->filter($message);
        } else {
            return $message;
        }
    }
}

$f1 = new FilterStrict();
$f2 = new FilterWarning();
$f3 = new FilterMobile();

$f1->setNext($f2);
$f2->setNext($f3);

$m1 = "现在开始测试链条1：语句中不包含敏感词，需要替换掉打架这种词，然后给手机号加上星：13333333333，这样的数据才可以对外展示哦";
echo $f1->filter($m1);
echo PHP_EOL;

$m2 = "现在开始测试链条2：这条语句走不到后面，因为包含了毒X，直接就报错了！！！语句中不包含敏感词，需要替换掉打架这种词，然后给手机号加上星：13333333333，这样的数据才可以对外展示哦";
echo $f1->filter($m2);
echo PHP_EOL;

*/

/**
 * =================================================================
 *                          装饰器模式
 * =================================================================
 */

/*
interface MessageTemplate
{
    public function message();
}


class CoupanMessageTemplate implements MessageTemplate
{
    public function message()
    {
        return '优惠券信息：我们是全国第一的牛X产品哦，送您十张优惠券！';
    }
}


abstract class DecoratorMessageTemplate implements MessageTemplate
{
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
}

class AdFilterDecoratorMessage extends DecoratorMessageTemplate
{
    public function message()
    {
        return str_replace('全国第一', '全国第二', $this->template->message());
    }
}

class SensitiveFilterDecoratorMessage extends DecoratorMessageTemplate
{
    public function message()
    {
        return str_replace('牛X', '好用', $this->template->message());
    }
}

// 客户端，发送接口，需要使用模板来进行短信发送
class Message
{
    public $msgType = 'old';
    public function send(MessageTemplate $mt)
    {
        // 发送出去咯
        if ($this->msgType == 'old') {
            echo '面向内网用户发送' . $mt->message() . PHP_EOL;
        } else if ($this->msgType == 'new') {
            echo '面向全网用户发送' . $mt->message() . PHP_EOL;
        }
    }
}

$template = new CoupanMessageTemplate();
$message = new Message();
$message->send($template);

$message->msgType = 'new';
$template = new AdFilterDecoratorMessage($template);
$message->send($template);

$template = new SensitiveFilterDecoratorMessage($template);
$message->send($template);

*/

/**
 * =================================================================
 *                          适配器模式
 * =================================================================
 */

interface Message
{

}