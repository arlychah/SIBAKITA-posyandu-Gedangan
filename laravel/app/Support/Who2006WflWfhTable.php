<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * WHO Child Growth Standards 2006 LMS tables.
 * Compact M/S value pairs are exact exports from WHO's 0.5 cm XLSX tables.
 */
final class Who2006WflWfhTable
{
    public const VERSION = 'WHO Child Growth Standards 2006';
    private const STEP_CM = 0.5;
    private static array $cache = [];

    public const SOURCES = [
        'wfl_boys' => 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfl_boys_0-to-2-years_zscores.xlsx?sfvrsn=e27a9da3_7',
        'wfl_girls' => 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfl_girls_0-to-2-years_zscores.xlsx?sfvrsn=288bc4e4_7',
        'wfh_boys' => 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfh_boys_2-to-5-years_zscores.xlsx?sfvrsn=202c0545_7',
        'wfh_girls' => 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfh_girls_2-to-5-years_zscores.xlsx?sfvrsn=4d66af6a_7',
    ];

    private const TABLES = [
        'wfl_boys' => [45.0, -0.3521, 'eNpllluuICsIRSd0U5E3pOc/r8uRh+nTf1UrIogbED9mOOf8d74T4Jiff/ATZB4mVEyP2TDkZgHU7EQzM5FhKsVc86cZabFgH7/n2A+j79D48LBm6cGHXZNkiKbDIIoR2qzzH/TDmACGKRQTWlunZsrr108zU+z43KyZO/Cwm7Y//J2juw6aAcmsU2+GGjRMmvHRiUWxmbBOLBJYTP3nq5hSMSea8wpJsbCY/aRyKh+gdO6dKxb50HDywiLFGOY+nOkUS2fjl+8xk9mxORs5FUtvsx/dtCWLOHMO4ss0Y/G1LQ1p5mV93Jv+YRSbP4y2FZTxgRbFVHB8oGoxM58cYJ1Nvzg4uUK+zL6Do1PH0qR98HKPGMVy490PqRj5i++mLZkceqx9ZC7WFtrWyM4yLOaC+puF2tvv5srzE946LQbxzlG14B+d1Qti2zLir7N51oJsXkpDnrWwOsDSuH8msbFUDXrWdOx+0j7CfPcrHcSV+7IoBvFiMS2G8c5buoqsD9hYvG0zz3u2kGJ6ePVX9RupU6JlVszP0yT0Og/ddZXn+NHuahKuLVSsC70h2PO8EJVjoTYk2VxTaSEhs8nf4k+Y2ZS/T5NQSd6xx9wIFzo3zNJ6hTIr415v38KtboAvs7ZXw9oQ5Gms+mNC1Fd8Je6E5DAJgfCG8lIMPuZ6q6Rg9a+ExjNksqvOnq7bxQFnz4gNCeraAD+ATcgJbYjkC2uYJczqnhs+pcCE2Qvn7Ee8oZ490eGBhjO8/PB4d9oKu3V/YYjuymqhkDPNeFeiNoRYYWcfapgylX8gI8U/5qkQXhgNVex3SDnFjHWDn5BymvjCknfOMdh5d0QbAuGGpLMyy329mzcke5mvlp0wa3Vv88yeSu82cVaa7mQFioYe21JByrtk394sgQ1MVf2WomQ+V3XXz4X53InVPDUUp9e3Zk97j4weQgnd6Hdn0Axp2zz1njnrXn+8Hf/CNN4BGGPOttfBfe852lAXyuxpvnOiZ3nC4B2+912RMKdbhOzrIBqm8T4t1BumwN57Y1aKu+zDZGA+7PYFo+MocOWtUSHlnHrz22pmJswDTebvE/H8D5IJDnA='],
        'wfl_girls' => [45.0, -0.3833, 'eNplllkO3DAIhi9UReyLev97lTE2nknfok/BP5vB9IiBA/yBBxIoAeAvPSo6jLmZMdgwb+aoephAs4B7nshmYZdFs3TBw5Q+jB8wHg21Zrg+mhk0o2WwmTRjxRgWzURvHL419EvDt4a5ymGxNTxx/AttlggTb6xcyVMJosOSm6Hm2GY0o/Bji0DNBD2HWTOVPHF8HFjMgnyYNAs0GrY18uPnZvQx/qsPBI8taTNC52HRjHXqhgzNJFiGUTNDxWHSzEXvf1sjXMY/XrHZA4D3P2+Gn9BejBTxbcvO8athj6Rcxs0M5WpgM2ea/7rH7amajgZtjbSbU1rneeUPb56zGa5Eb6bNeHXTd9286huv+vqjFOMLYDPj6Q3I/Z/LxAa5zyuf87cnvWquw3z5Fw9oXibN0Bh+70c8ZFNLMGrGZq87GDUj/DJopja9C8LNzFx+Z0Q8bjYaXY94wqaW0D0ZlfupB3Q/Z8Whc7eQmqHkxLGcKUZy7n5kZjPm0/fFtq3QOS8ytq3iiTfSt61BXLZtHVAPs23rqaOh2zby/qfbNiNHV1ZOcX3jQNgQE0e5b1dBWoIN+7oWFJAx714tqGs6NMTzp5HxwHPmmr4HHvVQnzzC8TPdJkhoIazkn0qvOdiQ8IyuiB57BVlYB/qGYiEDdUPNcf7CSj/+B0Psf9iV2rCdp4+f9nKJHiLKl/NUfvo7THrEiV8Jqe34VY51NT+wxAfiMQ8RfpWjFp/Fu3C1+XKcPyWuNYcar2aoPcc5zq8afqDYFZJjrl9dp7qho0yD2VGvYsRvvxfMufSVuRaqbZc6Qj2VChLxjAI4kHX2DuCBEneo0TnT8Gtq8IaumL/PiIIRMfNAu5dqwdF9XFhuiF8TK3jDGj/xO2YLipr8zu2CBmK/S7Ogq87W5CNUx8/a3EmuPWcyu95hw9t11Qu2Id/tTLvrrJ4AsxKpV05BxzEnPuYRs1BJO0u1sXReC+S4IZGNeT9dCgpM4Rh8Q40LezEWrCl+UsdyhGq+nSzx7qXaMzL7koM2rDfS8VNWN/wDClcOsw=='],
        'wfh_boys' => [65.0, -0.3521, 'eNpllVmOHTEIRTcUlZgHZf/7Cs8Gql/6zzoCGzBc/BEmB/gDDwRhneCvP6ogy+QyM+dldJkHy/8sMmwZf1g8gEzf98WD5PT9bjwkCMMIL2OVjY/sMrFYO247DYhlfZ/lm5t0LAG88Slclsi67PjmAyeCy0wuQ8qN2eky4jc+j8uYc+MLvUyElmX7qvDGnO1rImPHwJe5kC5ru+CNhbHvSxZcdt7FOpPLQm+ImC+MhpWw/rJkQPp1Z73NC7GhhLxRjqV64HeKBauVtmYJDd1xi+HZMJwW2rinv62j9yGss+5Dgg0xbT+bxrIi3q6FbCi0X4Y57iovdGloDhM8qjX03IyQo2HijgzedkZ6QHDixK4SPWhbOuiC0EPhYwluDQUiB962LKintBfqQJPcO2UecosJCTpOeiJMFp5WQn4AtjeBoiGegjXUhjVutlAa1tjiL8ualA2JvKGGw76ODR1ed7aGVyg6I25YfbwZ6XWXBz4uUyVoiIFvPceyZmzvDG4oFLTf4Q31FSK8A1ewykn7xdkwcEcYxRrmEa0L7RZEP+eFMZAwdjrAGrL628nZUDJe3aKGJviKlDf0UN/ZHMvklSTuL7ZPyqsh3Uv20NtgHNiwTvOQwLgry8L+OKsqbdOKzZ2lFtN1krfy/um6SVN7ZLymY/9IdSCTrmVAQ4mVNcOxtKMRF8rAu0wu9Hk9k8bd4cYZP2fTO6P4qfDeulQrCHWqdCI60FIW9nDVEnKdgkQXpDaO7p2R1rD21lhm/1HtEt79nN3etThwmyFbwWpLwMhv1kJrmDkPZcsF1UbI+c2E24oFOaSrlIjWsCQ9B4o3dJ+BLdUbeGbnQrodQqXJNppcSyQbsvHC4/SBajOwpz8udI0YeOWiYKottJMmlage+b7wNlhBVp6Q5IjVP/Fkw+4='],
        'wfh_girls' => [65.0, -0.3833, 'eNplllmy3DAIRTeUcjGDKvvfV7AY/JL8uU6DQOIC7Q8JEMAveOAgMgD89oeVeBicYmLvj82kmHp6F4PTdhZgy9rOw9cuolicPQ+i457j6+v+sngQkJZhMQI+w0yLMWgM01NMrkczLqbgaycdw36yjuHgmx93jLinFKMoll+6jJqdWDu0l538pI1Rb3oeDNu7ARcjj36DOKd92YGHRfuKoS9rX1VeO29fE93zrH39fb5h7RvMMUzbN0u0571PAL8xvynOQOGGSGe92RsSMy2khsxnzyRrKOJ7GcKGqt9L4Fia40bHOfPTV6YWDbPkuPAWEDGTl80TqCHKlCGiZJyQ7OBCb5i5y0JrqLh51jO90ETkP5iX1P9gHqT/nEmZ53TgRqfMc2S6eVLqhfZBABvyNlJCaahXng3nTEsd7XuO++29gdowa7iBqMTAD+CPwmlDFNStOzYkU/lHIfzwvXFrSRoqyspGx91+yKbaO6Hf5m8dz5mZ8gbySkkyz09LIQ2z7l+rDSRX354cKHekdEPPmcrftGoly6vPb2x4wxyJ2+dyGmbz7FC0kqJmJt+U6OT1IRL7e3wmZAvfeTzuivPIWUxraLbu2OXQJ9DWUsby3KFX0MrSsrX3mlhDOSHFTkc83FAkJnmqEZfQbsyC/Ur2vDoeKON+ZMSQg6Gie0antfRoSMdnvlK3TC4o1xnE3DXKbaQ2N2LmhimAhVpPF9lxvrD2TELCXVx3ZF4osHlKX/NdITIpSa0GfPfFNEKqu2Augjv+C7Zoc+rDblKJgfJu0oIKA+0WtiANDDxrWdWkO6gX2kDimJTueL1QdP8DGAw0m8FyjAaGT8sc60B4N/1AG8hfy1gMVMJ5pdqvL3ReLTkNzMuNu9eaphyAttd0G5gXGoF5dUdC+/41BAwMXNncNoM/rEXE9w==' ]
    ];

    public static function lookup(string $indicator, string $sex, float $statureCm): ?array
    {
        $key = strtolower($indicator . '_' . $sex);
        if (!isset(self::TABLES[$key])) {
            throw new InvalidArgumentException('Referensi WHO yang diminta tidak valid.');
        }

        [$minimum, $lambda, $packed] = self::TABLES[$key];
        $rows = self::decode($key, $packed);
        $index = ($statureCm - $minimum) / self::STEP_CM;
        if ($index < 0 || $index > count($rows) - 1) {
            return null;
        }

        $lowerIndex = (int) floor($index);
        $upperIndex = min($lowerIndex + 1, count($rows) - 1);
        $ratio = $index - $lowerIndex;
        $lower = $rows[$lowerIndex];
        $upper = $rows[$upperIndex];

        return [
            'l' => $lambda,
            'm' => $lower['m'] + (($upper['m'] - $lower['m']) * $ratio),
            's' => $lower['s'] + (($upper['s'] - $lower['s']) * $ratio),
            'min_cm' => $minimum,
            'max_cm' => $minimum + (count($rows) - 1) * self::STEP_CM,
        ];
    }

    private static function decode(string $key, string $packed): array
    {
        if (!isset(self::$cache[$key])) {
            $pairs = explode(';', gzuncompress(base64_decode($packed, true)));
            self::$cache[$key] = array_map(static function (string $pair): array {
                [$median, $coefficient] = array_map('floatval', explode(',', $pair));
                return ['m' => $median, 's' => $coefficient];
            }, $pairs);
        }

        return self::$cache[$key];
    }
}
