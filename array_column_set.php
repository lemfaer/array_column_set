<?php

/**
 * Set array column value with values from another array
 *
 * @param array &$array array to fill
 * @param int|string $target destination column
 * @param mixed $value value or values to put into
 * @param int|string $source source column
 *
 * @return boolean success
 */
function array_column_set(&$array, $target, $value, $source = null)
{
    if (!is_array($array)) {
        return false;
    }

    if (is_array($value) && func_num_args() > 3) {
        if (count($array) !== count($value)) {
            return false;
        }
    }

    foreach ($array as $i => &$item) {
        if (is_array($value) && func_num_args() > 3) {
            $current = array_shift($value);
            if (isset($source)) {
                if (is_array($current)) {
                    $current = $current[$source];
                } else if (is_object($current)) {
                    $current = $current->{$source};
                }
            }
        } else {
            $current = $value;
        }

        if (is_array($item)) {
            $item[$target] = $current;
        } else if (is_object($item)) {
            $item->{$target} = $current;
        }
    }

    return true;
}

if (
    !class_exists('PHPUnit_Framework_TestCase')
    && class_exists('\PHPUnit\Framework\TestCase')
) {
    class_alias(
        '\PHPUnit\Framework\TestCase',
        'PHPUnit_Framework_TestCase'
    );
}

if (class_exists('PHPUnit_Framework_TestCase')) {
    class TestArrayColumnSet extends PHPUnit_Framework_TestCase
    {
        /**
         * @covers array_column_set
         * @dataProvider provider_column_set
         */
        function test_column_set($args, $reference, $expected)
        {
            $array = array_shift($args);
            array_splice($args, 0, 0, array(&$array));
            $result = call_user_func_array("array_column_set", $args);
            $this->assertSame($reference, $array);
            $this->assertSame($expected, $result);
        }

        function provider_column_set()
        {
            return array(
                "empty" => array(
                    "args" => array(
                        "array" => array(),
                        "target" => "",
                        "value" => array()
                    ),
                    "reference" => array(),
                    "expected" => true
                ),

                "default_value" => array(
                    "args" => array(
                        "array" => array(
                            "abc" => array(),
                            "cba" => array(),
                            1 => array()
                        ),
                        "target" => "topic",
                        "value" => null
                    ),
                    "reference" => array(
                        "abc" => array(
                            "topic" => null
                        ),
                        "cba" => array(
                            "topic" => null
                        ),
                        1 => array(
                            "topic" => null
                        )
                    ),
                    "expected" => true
                ),

                "default_array" => array(
                    "args" => array(
                        "array" => array(
                            "abc" => array(),
                            "cba" => array(),
                            1 => array()
                        ),
                        "target" => "topic",
                        "value" => array()
                    ),
                    "reference" => array(
                        "abc" => array(
                            "topic" => array()
                        ),
                        "cba" => array(
                            "topic" => array()
                        ),
                        1 => array(
                            "topic" => array()
                        )
                    ),
                    "expected" => true
                ),

                "wrong_count" => array(
                    "args" => array(
                        "array" => array(
                            "abc" => array(),
                            "cba" => array(),
                            1 => array()
                        ),
                        "target" => "topic",
                        "value" => array(),
                        null
                    ),
                    "reference" => array(
                        "abc" => array(),
                        "cba" => array(),
                        1 => array()
                    ),
                    "expected" => false
                ),

                "no_source_key" => array(
                    "args" => array(
                        "array" => array(
                            "abc" => array(),
                            "cba" => array(),
                            1 => array()
                        ),
                        "target" => "topic",
                        "value" => array(
                            array("abc"),
                            array("cba"),
                            array("12")
                        ),
                        null
                    ),
                    "reference" => array(
                        "abc" => array(
                            "topic" => array("abc")
                        ),
                        "cba" => array(
                            "topic" => array("cba")
                        ),
                        1 => array(
                            "topic" => array("12")
                        )
                    ),
                    "expected" => true
                ),

                "source_key" => array(
                    "args" => array(
                        "array" => array(
                            "abc" => array(),
                            "cba" => array(),
                            1 => array()
                        ),
                        "target" => "topic",
                        "value" => array(
                            array(
                                "rocket" => "123",
                                "token" => "abc"
                            ),
                            array(
                                "rocket" => "123",
                                "token" => "cba"
                            ),
                            array(
                                "rocket" => "123",
                                "token" => "12"
                            )
                        ),
                        "token"
                    ),
                    "reference" => array(
                        "abc" => array(
                            "topic" => "abc"
                        ),
                        "cba" => array(
                            "topic" => "cba"
                        ),
                        1 => array(
                            "topic" => "12"
                        )
                    ),
                    "expected" => true
                )
            );
        }
    }
}
