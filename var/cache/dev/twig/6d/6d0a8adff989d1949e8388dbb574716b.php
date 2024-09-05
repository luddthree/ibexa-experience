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

/* @ludvik/matrix_fieldtype/content_fields.html.twig */
class __TwigTemplate_99dc35b6544034ed84ce509870266d0f extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ezmatrix_field' => [$this, 'block_ezmatrix_field'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/matrix_fieldtype/content_fields.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/matrix_fieldtype/content_fields.html.twig"));

        // line 1
        yield from $this->unwrap()->yieldBlock('ezmatrix_field', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    public function block_ezmatrix_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezmatrix_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezmatrix_field"));

        // line 2
        $___internal_parse_35_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 3
            yield "    ";
            $context["columnsSettings"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 3, $this->source); })()), "columns", [], "array", false, false, false, 3);
            // line 4
            yield "    ";
            $context["fieldData"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 4, $this->source); })()), "value", [], "any", false, false, false, 4), "rows", [], "any", false, false, false, 4);
            // line 5
            yield "
    <table ";
            // line 6
            if (CoreExtension::getAttribute($this->env, $this->source, ($context["attr"] ?? null), "class", [], "any", true, true, false, 6)) {
                yield " class=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 6, $this->source); })()), "class", [], "any", false, false, false, 6), "html", null, true);
                yield "\"";
            }
            yield ">
        <thead>
            <tr>
                ";
            // line 9
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["columnsSettings"]) || array_key_exists("columnsSettings", $context) ? $context["columnsSettings"] : (function () { throw new RuntimeError('Variable "columnsSettings" does not exist.', 9, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
                // line 10
                yield "                    <th>";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["column"], "name", [], "array", false, false, false, 10), "html", null, true);
                yield "</th>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            yield "            </tr>
        </thead>
        <tbody>
        ";
            // line 15
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["fieldData"]) || array_key_exists("fieldData", $context) ? $context["fieldData"] : (function () { throw new RuntimeError('Variable "fieldData" does not exist.', 15, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["data"]) {
                // line 16
                yield "        <tr>
            ";
                // line 17
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable((isset($context["columnsSettings"]) || array_key_exists("columnsSettings", $context) ? $context["columnsSettings"] : (function () { throw new RuntimeError('Variable "columnsSettings" does not exist.', 17, $this->source); })()));
                foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
                    // line 18
                    yield "                <td>";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["data"], "cells", [], "any", false, true, false, 18), CoreExtension::getAttribute($this->env, $this->source, $context["column"], "identifier", [], "array", false, false, false, 18), [], "array", true, true, false, 18)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["data"], "cells", [], "any", false, true, false, 18), CoreExtension::getAttribute($this->env, $this->source, $context["column"], "identifier", [], "array", false, false, false, 18), [], "array", false, false, false, 18))) : ("")), "html", null, true);
                    yield "</td>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 20
                yield "        </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 22
            yield "        </tbody>
    </table>
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 2
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_35_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/matrix_fieldtype/content_fields.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  137 => 2,  131 => 22,  124 => 20,  115 => 18,  111 => 17,  108 => 16,  104 => 15,  99 => 12,  90 => 10,  86 => 9,  76 => 6,  73 => 5,  70 => 4,  67 => 3,  65 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% block ezmatrix_field %}
{% apply spaceless %}
    {% set columnsSettings = fieldSettings['columns'] %}
    {% set fieldData = field.value.rows %}

    <table {% if attr.class is defined %} class=\"{{attr.class}}\"{% endif %}>
        <thead>
            <tr>
                {% for column in columnsSettings %}
                    <th>{{ column['name'] }}</th>
                {% endfor %}
            </tr>
        </thead>
        <tbody>
        {% for data in fieldData %}
        <tr>
            {% for column in columnsSettings %}
                <td>{{ data.cells[column['identifier']]|default }}</td>
            {% endfor %}
        </tr>
        {% endfor %}
        </tbody>
    </table>
{% endapply %}
{% endblock %}
", "@ludvik/matrix_fieldtype/content_fields.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/fieldtype-matrix/src/bundle/Resources/views/themes/standard/matrix_fieldtype/content_fields.html.twig");
    }
}
