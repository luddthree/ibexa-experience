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

/* @IbexaFieldTypePage/fields/ezlandingpage.html.twig */
class __TwigTemplate_ffbbe1e86bd56c7a1d2e92e335008a2a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'ezlandingpage_field' => [$this, 'block_ezlandingpage_field'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@IbexaCore/content_fields.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaFieldTypePage/fields/ezlandingpage.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaFieldTypePage/fields/ezlandingpage.html.twig"));

        $this->parent = $this->loadTemplate("@IbexaCore/content_fields.html.twig", "@IbexaFieldTypePage/fields/ezlandingpage.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_ezlandingpage_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezlandingpage_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezlandingpage_field"));

        // line 4
        yield "    ";
        $context["field_value"] = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 5
            yield "        ";
            $context["page"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 5, $this->source); })()), "value", [], "any", false, false, false, 5), "page", [], "method", false, false, false, 5);
            // line 6
            yield "        ";
            yield from             $this->loadTemplate($this->extensions['Ibexa\Bundle\FieldTypePage\Twig\LayoutExtension']->getPageLayout((isset($context["page"]) || array_key_exists("page", $context) ? $context["page"] : (function () { throw new RuntimeError('Variable "page" does not exist.', 6, $this->source); })())), "@IbexaFieldTypePage/fields/ezlandingpage.html.twig", 6)->unwrap()->yield(CoreExtension::merge($context, ["zones" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["page"]) || array_key_exists("page", $context) ? $context["page"] : (function () { throw new RuntimeError('Variable "page" does not exist.', 6, $this->source); })()), "zones", [], "any", false, false, false, 6)]));
            // line 7
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 8
        yield "    ";
        yield from         $this->unwrap()->yieldBlock("simple_block_field", $context, $blocks);
        yield "
    ";
        // line 9
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 9, $this->source); })()), "editorial_mode", [], "any", false, false, false, 9)) {
            // line 10
            yield "        ";
            yield $this->extensions['Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension']->renderWebpackLinkTags("ibexa-page-fieldtype-common-css", null, "ibexa");
            yield "
        ";
            // line 11
            $context["hidden_blocks"] = [];
            // line 12
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["page"]) || array_key_exists("page", $context) ? $context["page"] : (function () { throw new RuntimeError('Variable "page" does not exist.', 12, $this->source); })()), "zones", [], "any", false, false, false, 12));
            foreach ($context['_seq'] as $context["_key"] => $context["zone"]) {
                // line 13
                yield "            ";
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["zone"], "blocks", [], "any", false, false, false, 13));
                foreach ($context['_seq'] as $context["_key"] => $context["block"]) {
                    // line 14
                    yield "                ";
                    if ( !CoreExtension::getAttribute($this->env, $this->source, $context["block"], "isVisible", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 14, $this->source); })()), "reference_date_time", [], "any", false, false, false, 14)], "method", false, false, false, 14)) {
                        // line 15
                        yield "                    ";
                        $context["hidden_blocks"] = Twig\Extension\CoreExtension::merge((isset($context["hidden_blocks"]) || array_key_exists("hidden_blocks", $context) ? $context["hidden_blocks"] : (function () { throw new RuntimeError('Variable "hidden_blocks" does not exist.', 15, $this->source); })()), [CoreExtension::getAttribute($this->env, $this->source, $context["block"], "id", [], "any", false, false, false, 15)]);
                        // line 16
                        yield "                ";
                    }
                    // line 17
                    yield "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['block'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 18
                yield "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['zone'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            yield "        <script type=\"text/javascript\">
            (function(global, doc) {
                'use strict';

                const hiddenBlocks = ";
            // line 23
            yield json_encode((isset($context["hidden_blocks"]) || array_key_exists("hidden_blocks", $context) ? $context["hidden_blocks"] : (function () { throw new RuntimeError('Variable "hidden_blocks" does not exist.', 23, $this->source); })()));
            yield ";

                hiddenBlocks.forEach((id) => {
                    const element = doc.querySelector(`.landing-page__block[data-ez-block-id=\"\${id}\"]`);

                    if (element) {
                        [...element.childNodes].forEach((child) => {
                            if (child instanceof HTMLElement) {
                                child.classList.add('ibexa-mark-invisible');
                            }
                        });
                    }
                });
            })(window, window.document);
        </script>
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@IbexaFieldTypePage/fields/ezlandingpage.html.twig";
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
        return array (  133 => 23,  127 => 19,  121 => 18,  115 => 17,  112 => 16,  109 => 15,  106 => 14,  101 => 13,  96 => 12,  94 => 11,  89 => 10,  87 => 9,  82 => 8,  78 => 7,  75 => 6,  72 => 5,  69 => 4,  59 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"@IbexaCore/content_fields.html.twig\" %}

{% block ezlandingpage_field %}
    {% set field_value %}
        {% set page = field.value.page() %}
        {% include ibexa_page_layout(page) with {'zones': page.zones} %}
    {% endset %}
    {{ block('simple_block_field') }}
    {% if parameters.editorial_mode %}
        {{ encore_entry_link_tags('ibexa-page-fieldtype-common-css', null, 'ibexa') }}
        {% set hidden_blocks = [] %}
        {% for zone in page.zones %}
            {% for block in zone.blocks %}
                {% if not block.isVisible(parameters.reference_date_time) %}
                    {% set hidden_blocks = hidden_blocks|merge([block.id]) %}
                {% endif %}
            {% endfor %}
        {% endfor %}
        <script type=\"text/javascript\">
            (function(global, doc) {
                'use strict';

                const hiddenBlocks = {{ hidden_blocks|json_encode|raw }};

                hiddenBlocks.forEach((id) => {
                    const element = doc.querySelector(`.landing-page__block[data-ez-block-id=\"\${id}\"]`);

                    if (element) {
                        [...element.childNodes].forEach((child) => {
                            if (child instanceof HTMLElement) {
                                child.classList.add('ibexa-mark-invisible');
                            }
                        });
                    }
                });
            })(window, window.document);
        </script>
    {% endif %}
{% endblock %}
", "@IbexaFieldTypePage/fields/ezlandingpage.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/fieldtype-page/src/bundle/Resources/views/fields/ezlandingpage.html.twig");
    }
}
