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

/* @ludvik/product_catalog/field_type/product.html.twig */
class __TwigTemplate_09ba1c7f909f3afd42bccaac41f7a0fb extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ibexa_product_specification_field' => [$this, 'block_ibexa_product_specification_field'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/product_catalog/field_type/product.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/product_catalog/field_type/product.html.twig"));

        // line 1
        yield from $this->unwrap()->yieldBlock('ibexa_product_specification_field', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    public function block_ibexa_product_specification_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_product_specification_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_product_specification_field"));

        // line 2
        yield "    ";
        $context["product"] = $this->env->getRuntime('Ibexa\Bundle\ProductCatalog\Twig\RenderProductRuntime')->getProduct((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 2, $this->source); })()));
        // line 3
        yield "    ";
        $___internal_parse_34_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 4
            yield "        <table>
            <tbody>
            ";
            // line 6
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 6, $this->source); })()), "attributes", [], "any", false, false, false, 6));
            foreach ($context['_seq'] as $context["_key"] => $context["attribute"]) {
                // line 7
                yield "                ";
                $context["definition"] = CoreExtension::getAttribute($this->env, $this->source, $context["attribute"], "getAttributeDefinition", [], "method", false, false, false, 7);
                // line 8
                yield "                <tr>
                    <th>";
                // line 9
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["definition"]) || array_key_exists("definition", $context) ? $context["definition"] : (function () { throw new RuntimeError('Variable "definition" does not exist.', 9, $this->source); })()), "getName", [], "method", false, false, false, 9), "html", null, true);
                yield "</th>
                    <td>";
                // line 10
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Ibexa\Bundle\ProductCatalog\Twig\RenderProductRuntime')->formatAttributeValue($context["attribute"]), "html", null, true);
                yield "</td>
                </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attribute'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 13
            yield "            </tbody>
        </table>
    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 3
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_34_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/product_catalog/field_type/product.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  104 => 3,  98 => 13,  89 => 10,  85 => 9,  82 => 8,  79 => 7,  75 => 6,  71 => 4,  68 => 3,  65 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% block ibexa_product_specification_field %}
    {% set product = content|ibexa_get_product %}
    {% apply spaceless %}
        <table>
            <tbody>
            {% for attribute in product.attributes %}
                {% set definition = attribute.getAttributeDefinition() %}
                <tr>
                    <th>{{ definition.getName() }}</th>
                    <td>{{ attribute|ibexa_format_product_attribute }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    {% endapply %}
{% endblock %}
", "@ludvik/product_catalog/field_type/product.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/product-catalog/src/bundle/Resources/views/themes/standard/product_catalog/field_type/product.html.twig");
    }
}
