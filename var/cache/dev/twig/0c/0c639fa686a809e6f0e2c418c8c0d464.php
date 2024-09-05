<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @IbexaDebug/Profiler/layout.html.twig */
class __TwigTemplate_3b0f2b4624396f36de487f51eb231e85 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'toolbar' => [$this, 'block_toolbar'],
            'menu' => [$this, 'block_menu'],
            'panel' => [$this, 'block_panel'],
        ];
        $macros["_self"] = $this->macros["_self"] = $this;
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaDebug/Profiler/layout.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaDebug/Profiler/layout.html.twig"));

        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@IbexaDebug/Profiler/layout.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 16
    public function block_toolbar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        // line 17
        yield "    ";
        $context["icon"] = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 18
            yield "        ";
            yield CoreExtension::callMacro($macros["_self"], "macro_ibexa_logo", [], 18, $context, $this->getSourceContext());
            yield "
    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 20
        yield "
    ";
        // line 21
        $context["text"] = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 22
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 22, $this->source); })()), "allCollectors", [], "any", false, false, false, 22));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["name"] => $context["inner_collector"]) {
                // line 23
                yield "            ";
                $context["inner_template"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 23, $this->source); })()), "getToolbarTemplate", [$context["name"]], "method", false, false, false, 23);
                // line 24
                yield "            ";
                if ((isset($context["inner_template"]) || array_key_exists("inner_template", $context) ? $context["inner_template"] : (function () { throw new RuntimeError('Variable "inner_template" does not exist.', 24, $this->source); })())) {
                    // line 25
                    yield "                ";
                    yield from                     $this->loadTemplate((isset($context["inner_template"]) || array_key_exists("inner_template", $context) ? $context["inner_template"] : (function () { throw new RuntimeError('Variable "inner_template" does not exist.', 25, $this->source); })()), "@IbexaDebug/Profiler/layout.html.twig", 25)->unwrap()->yield(CoreExtension::merge($context, ["collector" => $context["inner_collector"]]));
                    // line 26
                    yield "
                ";
                    // line 27
                    if ( !CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 27)) {
                        yield "<hr />";
                    }
                    // line 28
                    yield "            ";
                }
                // line 29
                yield "
        ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['inner_collector'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 31
            yield "
    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 33
        yield "
    ";
        // line 35
        yield "    ";
        $context["stats"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 35, $this->source); })()), "getCollector", ["ezpublish.debug.persistence"], "method", false, false, false, 35), "stats", [], "any", false, false, false, 35);
        // line 36
        yield "    ";
        $context["total_uncached"] = (CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 36, $this->source); })()), "uncached", [], "any", false, false, false, 36) + CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 36, $this->source); })()), "miss", [], "any", false, false, false, 36));
        // line 37
        yield "    ";
        $context["status_logo"] = ((((isset($context["total_uncached"]) || array_key_exists("total_uncached", $context) ? $context["total_uncached"] : (function () { throw new RuntimeError('Variable "total_uncached" does not exist.', 37, $this->source); })()) > 100)) ? ("red") : (((((isset($context["total_uncached"]) || array_key_exists("total_uncached", $context) ? $context["total_uncached"] : (function () { throw new RuntimeError('Variable "total_uncached" does not exist.', 37, $this->source); })()) > 15)) ? ("yellow") : ((((CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 37, $this->source); })()), "hit", [], "any", false, false, false, 37) > 100)) ? ("yellow") : (""))))));
        // line 38
        yield "
    ";
        // line 39
        yield Twig\Extension\CoreExtension::include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", ["link" => (isset($context["profiler_url"]) || array_key_exists("profiler_url", $context) ? $context["profiler_url"] : (function () { throw new RuntimeError('Variable "profiler_url" does not exist.', 39, $this->source); })()), "status" => ((array_key_exists("status_logo", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["status_logo"]) || array_key_exists("status_logo", $context) ? $context["status_logo"] : (function () { throw new RuntimeError('Variable "status_logo" does not exist.', 39, $this->source); })()), "")) : (""))]);
        yield "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 42
    public function block_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        // line 43
        yield "    <span class=\"label\">
        <span class=\"icon\">
            ";
        // line 45
        yield CoreExtension::callMacro($macros["_self"], "macro_ibexa_logo", [], 45, $context, $this->getSourceContext());
        yield "
        </span>
        <strong>Ibexa DXP</strong>
    </span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 51
    public function block_panel($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        // line 52
        yield "    <h2>Usage Information</h2>

    ";
        // line 54
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 54, $this->source); })()), "allCollectors", [], "any", false, false, false, 54));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["name"] => $context["inner_collector"]) {
            // line 55
            yield "        ";
            $context["inner_template"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 55, $this->source); })()), "getPanelTemplate", [$context["name"]], "method", false, false, false, 55);
            // line 56
            yield "        ";
            if ((isset($context["inner_template"]) || array_key_exists("inner_template", $context) ? $context["inner_template"] : (function () { throw new RuntimeError('Variable "inner_template" does not exist.', 56, $this->source); })())) {
                yield from                 $this->loadTemplate((isset($context["inner_template"]) || array_key_exists("inner_template", $context) ? $context["inner_template"] : (function () { throw new RuntimeError('Variable "inner_template" does not exist.', 56, $this->source); })()), "@IbexaDebug/Profiler/layout.html.twig", 56)->unwrap()->yield(CoreExtension::merge($context, ["collector" => $context["inner_collector"]]));
            }
            // line 57
            yield "
        ";
            // line 58
            if ( !CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 58)) {
                yield "<hr />";
            }
            // line 59
            yield "
    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['name'], $context['inner_collector'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 61
        yield "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 3
    public function macro_ibexa_logo(...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "ibexa_logo"));

            $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "ibexa_logo"));

            // line 0
            yield "    <svg width=\"186\" height=\"271\" viewBox=\"0 0 186 271\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">
        <path d=\"M0.320007 55.5L0.350005 5.26999C0.350005 2.76999 2.38001 0.73999 4.89001 0.73999H31.22C33.72 0.73999 35.75 2.76999 35.76 5.26999L35.8 22.47C35.82 28.61 33.39 34.5 29.05 38.84L8.88 59.04C5.71 62.2 0.320007 59.96 0.320007 55.5ZM185.57 227.5C185.59 235.72 182.31 243.61 176.48 249.4L156.32 269.41C153.89 271.83 149.75 270.09 149.76 266.66L149.87 243.8C134.15 256.12 114.36 263.47 92.88 263.47C40.36 263.47 -2.08999 219.5 0.420013 166.44L0.309997 114.37C0.289997 106.15 3.56999 98.26 9.39999 92.47L29.56 72.46C31.99 70.04 36.13 71.78 36.12 75.21L36.01 97.99C49.98 87.05 67.12 80.01 85.73 78.61C139.31 74.56 184.32 116.44 185.42 168.93L185.44 168.89V170.89C185.44 170.9 185.44 170.91 185.44 170.92C185.44 170.93 185.44 170.93 185.44 170.94L185.57 227.5ZM149.95 171.62C149.95 139.76 124.03 113.84 92.17 113.84C60.31 113.84 34.39 139.76 34.39 171.62C34.39 203.48 60.31 229.4 92.17 229.4C124.03 229.4 149.95 203.47 149.95 171.62Z\" fill=\"url(#paint0_linear_3_26)\"/>
        <defs>
            <linearGradient id=\"paint0_linear_3_26\" x1=\"0.314997\" y1=\"135.638\" x2=\"185.575\" y2=\"135.638\" gradientUnits=\"userSpaceOnUse\">
                <stop stop-color=\"#FF4713\"/>
                <stop offset=\"0.5\" stop-color=\"#DB0032\"/>
                <stop offset=\"1\" stop-color=\"#AE1164\"/>
            </linearGradient>
        </defs>
    </svg>
";
            
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

            
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@IbexaDebug/Profiler/layout.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  292 => 0,  275 => 3,  263 => 61,  248 => 59,  244 => 58,  241 => 57,  236 => 56,  233 => 55,  216 => 54,  212 => 52,  202 => 51,  186 => 45,  182 => 43,  172 => 42,  159 => 39,  156 => 38,  153 => 37,  150 => 36,  147 => 35,  144 => 33,  139 => 31,  124 => 29,  121 => 28,  117 => 27,  114 => 26,  111 => 25,  108 => 24,  105 => 23,  87 => 22,  85 => 21,  82 => 20,  75 => 18,  72 => 17,  62 => 16,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% macro ibexa_logo() %}
    <svg width=\"186\" height=\"271\" viewBox=\"0 0 186 271\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">
        <path d=\"M0.320007 55.5L0.350005 5.26999C0.350005 2.76999 2.38001 0.73999 4.89001 0.73999H31.22C33.72 0.73999 35.75 2.76999 35.76 5.26999L35.8 22.47C35.82 28.61 33.39 34.5 29.05 38.84L8.88 59.04C5.71 62.2 0.320007 59.96 0.320007 55.5ZM185.57 227.5C185.59 235.72 182.31 243.61 176.48 249.4L156.32 269.41C153.89 271.83 149.75 270.09 149.76 266.66L149.87 243.8C134.15 256.12 114.36 263.47 92.88 263.47C40.36 263.47 -2.08999 219.5 0.420013 166.44L0.309997 114.37C0.289997 106.15 3.56999 98.26 9.39999 92.47L29.56 72.46C31.99 70.04 36.13 71.78 36.12 75.21L36.01 97.99C49.98 87.05 67.12 80.01 85.73 78.61C139.31 74.56 184.32 116.44 185.42 168.93L185.44 168.89V170.89C185.44 170.9 185.44 170.91 185.44 170.92C185.44 170.93 185.44 170.93 185.44 170.94L185.57 227.5ZM149.95 171.62C149.95 139.76 124.03 113.84 92.17 113.84C60.31 113.84 34.39 139.76 34.39 171.62C34.39 203.48 60.31 229.4 92.17 229.4C124.03 229.4 149.95 203.47 149.95 171.62Z\" fill=\"url(#paint0_linear_3_26)\"/>
        <defs>
            <linearGradient id=\"paint0_linear_3_26\" x1=\"0.314997\" y1=\"135.638\" x2=\"185.575\" y2=\"135.638\" gradientUnits=\"userSpaceOnUse\">
                <stop stop-color=\"#FF4713\"/>
                <stop offset=\"0.5\" stop-color=\"#DB0032\"/>
                <stop offset=\"1\" stop-color=\"#AE1164\"/>
            </linearGradient>
        </defs>
    </svg>
{% endmacro %}

{% block toolbar %}
    {% set icon %}
        {{ _self.ibexa_logo() }}
    {% endset %}

    {% set text %}
        {% for name, inner_collector in collector.allCollectors %}
            {% set inner_template = collector.getToolbarTemplate( name ) %}
            {% if inner_template %}
                {% include inner_template with { \"collector\": inner_collector } %}

                {% if not loop.last %}<hr />{% endif %}
            {% endif %}

        {% endfor %}

    {% endset %}

    {# Set to red if over 100 uncached, and to yellow if either over 15 uncached or over 100 cache hits lookups #}
    {% set stats = collector.getCollector('ezpublish.debug.persistence').stats %}
    {% set total_uncached = stats.uncached + stats.miss %}
    {% set status_logo = total_uncached > 100 ? 'red' : (total_uncached > 15 ? 'yellow' : (stats.hit > 100 ? 'yellow' : '')) %}

    {{ include('@WebProfiler/Profiler/toolbar_item.html.twig', { link: profiler_url, status: status_logo|default('') }) }}
{% endblock %}

{% block menu %}
    <span class=\"label\">
        <span class=\"icon\">
            {{ _self.ibexa_logo() }}
        </span>
        <strong>Ibexa DXP</strong>
    </span>
{% endblock %}

{% block panel %}
    <h2>Usage Information</h2>

    {% for name, inner_collector in collector.allCollectors %}
        {% set inner_template = collector.getPanelTemplate( name ) %}
        {% if inner_template %}{% include inner_template with { \"collector\": inner_collector } %}{% endif %}

        {% if not loop.last %}<hr />{% endif %}

    {% endfor %}

{% endblock %}
", "@IbexaDebug/Profiler/layout.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/core/src/bundle/Debug/Resources/views/Profiler/layout.html.twig");
    }
}
