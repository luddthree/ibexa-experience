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

/* @IbexaCore/content_fields.html.twig */
class __TwigTemplate_025c67039ebcab107a5ff83462ed9cd7 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ezstring_field' => [$this, 'block_ezstring_field'],
            'eztext_field' => [$this, 'block_eztext_field'],
            'ezauthor_field' => [$this, 'block_ezauthor_field'],
            'ezcountry_field' => [$this, 'block_ezcountry_field'],
            'ezboolean_field' => [$this, 'block_ezboolean_field'],
            'ezdatetime_field' => [$this, 'block_ezdatetime_field'],
            'ezdate_field' => [$this, 'block_ezdate_field'],
            'eztime_field' => [$this, 'block_eztime_field'],
            'ezemail_field' => [$this, 'block_ezemail_field'],
            'ezinteger_field' => [$this, 'block_ezinteger_field'],
            'ezfloat_field' => [$this, 'block_ezfloat_field'],
            'ezurl_field' => [$this, 'block_ezurl_field'],
            'ezisbn_field' => [$this, 'block_ezisbn_field'],
            'ezkeyword_field' => [$this, 'block_ezkeyword_field'],
            'ezselection_field' => [$this, 'block_ezselection_field'],
            'ezuser_field' => [$this, 'block_ezuser_field'],
            'ezbinaryfile_field' => [$this, 'block_ezbinaryfile_field'],
            'ezmedia_field' => [$this, 'block_ezmedia_field'],
            'ezobjectrelationlist_field' => [$this, 'block_ezobjectrelationlist_field'],
            'ezgmaplocation_field' => [$this, 'block_ezgmaplocation_field'],
            'ezimage_field' => [$this, 'block_ezimage_field'],
            'ezimageasset_field' => [$this, 'block_ezimageasset_field'],
            'ezobjectrelation_field' => [$this, 'block_ezobjectrelation_field'],
            'simple_block_field' => [$this, 'block_simple_block_field'],
            'simple_inline_field' => [$this, 'block_simple_inline_field'],
            'field_attributes' => [$this, 'block_field_attributes'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaCore/content_fields.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaCore/content_fields.html.twig"));

        // line 12
        yield "
";
        // line 14
        yield "
";
        // line 15
        yield from $this->unwrap()->yieldBlock('ezstring_field', $context, $blocks);
        // line 21
        yield "
";
        // line 22
        yield from $this->unwrap()->yieldBlock('eztext_field', $context, $blocks);
        // line 28
        yield "
";
        // line 29
        yield from $this->unwrap()->yieldBlock('ezauthor_field', $context, $blocks);
        // line 40
        yield "
";
        // line 41
        yield from $this->unwrap()->yieldBlock('ezcountry_field', $context, $blocks);
        // line 58
        yield "
";
        // line 60
        yield from $this->unwrap()->yieldBlock('ezboolean_field', $context, $blocks);
        // line 66
        yield "
";
        // line 67
        yield from $this->unwrap()->yieldBlock('ezdatetime_field', $context, $blocks);
        // line 79
        yield "
";
        // line 80
        yield from $this->unwrap()->yieldBlock('ezdate_field', $context, $blocks);
        // line 88
        yield "
";
        // line 89
        yield from $this->unwrap()->yieldBlock('eztime_field', $context, $blocks);
        // line 101
        yield "
";
        // line 102
        yield from $this->unwrap()->yieldBlock('ezemail_field', $context, $blocks);
        // line 110
        yield "
";
        // line 111
        yield from $this->unwrap()->yieldBlock('ezinteger_field', $context, $blocks);
        // line 119
        yield "
";
        // line 121
        yield from $this->unwrap()->yieldBlock('ezfloat_field', $context, $blocks);
        // line 129
        yield "
";
        // line 130
        yield from $this->unwrap()->yieldBlock('ezurl_field', $context, $blocks);
        // line 138
        yield "
";
        // line 139
        yield from $this->unwrap()->yieldBlock('ezisbn_field', $context, $blocks);
        // line 145
        yield "
";
        // line 146
        yield from $this->unwrap()->yieldBlock('ezkeyword_field', $context, $blocks);
        // line 157
        yield "
";
        // line 158
        yield from $this->unwrap()->yieldBlock('ezselection_field', $context, $blocks);
        // line 182
        yield "
";
        // line 187
        yield from $this->unwrap()->yieldBlock('ezuser_field', $context, $blocks);
        // line 201
        yield "
";
        // line 202
        yield from $this->unwrap()->yieldBlock('ezbinaryfile_field', $context, $blocks);
        // line 216
        yield "
";
        // line 217
        yield from $this->unwrap()->yieldBlock('ezmedia_field', $context, $blocks);
        // line 277
        yield "
";
        // line 278
        yield from $this->unwrap()->yieldBlock('ezobjectrelationlist_field', $context, $blocks);
        // line 292
        yield "
";
        // line 296
        yield from $this->unwrap()->yieldBlock('ezgmaplocation_field', $context, $blocks);
        // line 425
        yield "
";
        // line 432
        yield from $this->unwrap()->yieldBlock('ezimage_field', $context, $blocks);
        // line 458
        yield "
";
        // line 459
        yield from $this->unwrap()->yieldBlock('ezimageasset_field', $context, $blocks);
        // line 475
        yield "
";
        // line 476
        yield from $this->unwrap()->yieldBlock('ezobjectrelation_field', $context, $blocks);
        // line 485
        yield "
";
        // line 488
        yield from $this->unwrap()->yieldBlock('simple_block_field', $context, $blocks);
        // line 498
        yield "
";
        // line 499
        yield from $this->unwrap()->yieldBlock('simple_inline_field', $context, $blocks);
        // line 507
        yield "
";
        // line 509
        yield from $this->unwrap()->yieldBlock('field_attributes', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    // line 15
    public function block_ezstring_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezstring_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezstring_field"));

        // line 16
        $___internal_parse_7_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 17
            yield "    ";
            $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 17, $this->source); })()), "value", [], "any", false, false, false, 17), "text", [], "any", false, false, false, 17);
            // line 18
            yield "    ";
            yield from             $this->unwrap()->yieldBlock("simple_inline_field", $context, $blocks);
            yield "
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 16
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_7_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 22
    public function block_eztext_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "eztext_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "eztext_field"));

        // line 23
        $___internal_parse_8_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 24
            yield "    ";
            $context["field_value"] = Twig\Extension\CoreExtension::nl2br($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 24, $this->source); })()), "value", [], "any", false, false, false, 24), "html", null, true));
            // line 25
            yield "    ";
            yield from             $this->unwrap()->yieldBlock("simple_block_field", $context, $blocks);
            yield "
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 23
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_8_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 29
    public function block_ezauthor_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezauthor_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezauthor_field"));

        // line 30
        $___internal_parse_9_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 31
            yield "    ";
            if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 31, $this->source); })()), "value", [], "any", false, false, false, 31), "authors", [], "any", false, false, false, 31)) > 0)) {
                // line 32
                yield "        <ul ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
        ";
                // line 33
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 33, $this->source); })()), "value", [], "any", false, false, false, 33), "authors", [], "any", false, false, false, 33));
                foreach ($context['_seq'] as $context["_key"] => $context["author"]) {
                    // line 34
                    yield "            <li><a href=\"mailto:";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["author"], "email", [], "any", false, false, false, 34), "url"), "html", null, true);
                    yield "\">";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["author"], "name", [], "any", false, false, false, 34), "html", null, true);
                    yield "</a></li>
        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['author'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 36
                yield "        </ul>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 30
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_9_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 41
    public function block_ezcountry_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezcountry_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezcountry_field"));

        // line 42
        $___internal_parse_10_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 43
            yield "    ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 43, $this->source); })()), "isMultiple", [], "any", false, false, false, 43) && (Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 43, $this->source); })()), "value", [], "any", false, false, false, 43), "countries", [], "any", false, false, false, 43)) > 0))) {
                // line 44
                yield "        <ul ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
            ";
                // line 45
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 45, $this->source); })()), "value", [], "any", false, false, false, 45), "countries", [], "any", false, false, false, 45));
                foreach ($context['_seq'] as $context["_key"] => $context["country"]) {
                    // line 46
                    yield "                <li>";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["country"], "Name", [], "array", false, false, false, 46), "html", null, true);
                    yield "</li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['country'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 48
                yield "        </ul>
    ";
            } elseif ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,             // line 49
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 49, $this->source); })()), "value", [], "any", false, false, false, 49), "countries", [], "any", false, false, false, 49)) == 1)) {
                // line 50
                yield "        <p ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
        ";
                // line 51
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 51, $this->source); })()), "value", [], "any", false, false, false, 51), "countries", [], "any", false, false, false, 51));
                foreach ($context['_seq'] as $context["_key"] => $context["country"]) {
                    // line 52
                    yield "            ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["country"], "Name", [], "array", false, false, false, 52), "html", null, true);
                    yield "
        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['country'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 54
                yield "        </p>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 42
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_10_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 60
    public function block_ezboolean_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezboolean_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezboolean_field"));

        // line 61
        $___internal_parse_11_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 62
            yield "    ";
            $context["field_value"] = ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 62, $this->source); })()), "value", [], "any", false, false, false, 62), "bool", [], "any", false, false, false, 62)) ? ("Yes") : ("No"));
            // line 63
            yield "    ";
            yield from             $this->unwrap()->yieldBlock("simple_inline_field", $context, $blocks);
            yield "
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 61
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_11_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 67
    public function block_ezdatetime_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezdatetime_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezdatetime_field"));

        // line 68
        $___internal_parse_12_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 69
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 69, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 69, $this->source); })()))) {
                // line 70
                yield "        ";
                if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 70, $this->source); })()), "useSeconds", [], "any", false, false, false, 70)) {
                    // line 71
                    yield "            ";
                    $context["field_value"] = $this->extensions['Twig\Extra\Intl\IntlExtension']->formatDateTime($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 71, $this->source); })()), "value", [], "any", false, false, false, 71), "value", [], "any", false, false, false, 71), "short", "medium", "", null, "gregorian", CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 71, $this->source); })()), "locale", [], "any", false, false, false, 71));
                    // line 72
                    yield "        ";
                } else {
                    // line 73
                    yield "            ";
                    $context["field_value"] = $this->extensions['Twig\Extra\Intl\IntlExtension']->formatDateTime($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 73, $this->source); })()), "value", [], "any", false, false, false, 73), "value", [], "any", false, false, false, 73), "short", "short", "", null, "gregorian", CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 73, $this->source); })()), "locale", [], "any", false, false, false, 73));
                    // line 74
                    yield "        ";
                }
                // line 75
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("simple_block_field", $context, $blocks);
                yield "
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 68
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_12_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 80
    public function block_ezdate_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezdate_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezdate_field"));

        // line 81
        $___internal_parse_13_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 82
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 82, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 82, $this->source); })()))) {
                // line 83
                yield "        ";
                $context["field_value"] = $this->extensions['Twig\Extra\Intl\IntlExtension']->formatDate($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 83, $this->source); })()), "value", [], "any", false, false, false, 83), "date", [], "any", false, false, false, 83), "short", "", "UTC", "gregorian", CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 83, $this->source); })()), "locale", [], "any", false, false, false, 83));
                // line 84
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("simple_block_field", $context, $blocks);
                yield "
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 81
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_13_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 89
    public function block_eztime_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "eztime_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "eztime_field"));

        // line 90
        $___internal_parse_14_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 91
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 91, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 91, $this->source); })()))) {
                // line 92
                yield "        ";
                if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 92, $this->source); })()), "useSeconds", [], "any", false, false, false, 92)) {
                    // line 93
                    yield "            ";
                    $context["field_value"] = $this->extensions['Twig\Extra\Intl\IntlExtension']->formatTime($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 93, $this->source); })()), "value", [], "any", false, false, false, 93), "time", [], "any", false, false, false, 93), "medium", "", "UTC", "gregorian", CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 93, $this->source); })()), "locale", [], "any", false, false, false, 93));
                    // line 94
                    yield "        ";
                } else {
                    // line 95
                    yield "            ";
                    $context["field_value"] = $this->extensions['Twig\Extra\Intl\IntlExtension']->formatTime($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 95, $this->source); })()), "value", [], "any", false, false, false, 95), "time", [], "any", false, false, false, 95), "short", "", "UTC", "gregorian", CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 95, $this->source); })()), "locale", [], "any", false, false, false, 95));
                    // line 96
                    yield "        ";
                }
                // line 97
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("simple_block_field", $context, $blocks);
                yield "
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 90
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_14_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 102
    public function block_ezemail_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezemail_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezemail_field"));

        // line 103
        $___internal_parse_15_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 104
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 104, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 104, $this->source); })()))) {
                // line 105
                yield "        ";
                $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 105, $this->source); })()), "value", [], "any", false, false, false, 105), "email", [], "any", false, false, false, 105);
                // line 106
                yield "        <a href=\"mailto:";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 106, $this->source); })()), "value", [], "any", false, false, false, 106), "email", [], "any", false, false, false, 106), "url"), "html", null, true);
                yield "\" ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 106, $this->source); })()), "value", [], "any", false, false, false, 106), "email", [], "any", false, false, false, 106), "html", null, true);
                yield "</a>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 103
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_15_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 111
    public function block_ezinteger_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezinteger_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezinteger_field"));

        // line 112
        $___internal_parse_16_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 113
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 113, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 113, $this->source); })()))) {
                // line 114
                yield "        ";
                $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 114, $this->source); })()), "value", [], "any", false, false, false, 114), "value", [], "any", false, false, false, 114);
                // line 115
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("simple_inline_field", $context, $blocks);
                yield "
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 112
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_16_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 121
    public function block_ezfloat_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezfloat_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezfloat_field"));

        // line 122
        $___internal_parse_17_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 123
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 123, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 123, $this->source); })()))) {
                // line 124
                yield "        ";
                $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 124, $this->source); })()), "value", [], "any", false, false, false, 124), "value", [], "any", false, false, false, 124);
                // line 125
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("simple_inline_field", $context, $blocks);
                yield "
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 122
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_17_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 130
    public function block_ezurl_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezurl_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezurl_field"));

        // line 131
        $___internal_parse_18_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 132
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 132, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 132, $this->source); })()))) {
                // line 133
                yield "        <a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 133, $this->source); })()), "value", [], "any", false, false, false, 133), "link", [], "any", false, false, false, 133), "html", null, true);
                yield "\"
            ";
                // line 134
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 134, $this->source); })()), "value", [], "any", false, false, false, 134), "text", [], "any", false, false, false, 134)) ? (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 134, $this->source); })()), "value", [], "any", false, false, false, 134), "text", [], "any", false, false, false, 134)) : (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 134, $this->source); })()), "value", [], "any", false, false, false, 134), "link", [], "any", false, false, false, 134))), "html", null, true);
                yield "</a>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 131
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_18_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 139
    public function block_ezisbn_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezisbn_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezisbn_field"));

        // line 140
        $___internal_parse_19_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 141
            yield "    ";
            $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 141, $this->source); })()), "value", [], "any", false, false, false, 141), "isbn", [], "any", false, false, false, 141);
            // line 142
            yield "    ";
            yield from             $this->unwrap()->yieldBlock("simple_inline_field", $context, $blocks);
            yield "
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 140
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_19_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 146
    public function block_ezkeyword_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezkeyword_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezkeyword_field"));

        // line 147
        $___internal_parse_20_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 148
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 148, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 148, $this->source); })()))) {
                // line 149
                yield "        <ul ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
        ";
                // line 150
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 150, $this->source); })()), "value", [], "any", false, false, false, 150), "values", [], "any", false, false, false, 150));
                foreach ($context['_seq'] as $context["_key"] => $context["keyword"]) {
                    // line 151
                    yield "            <li>";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["keyword"], "html", null, true);
                    yield "</li>
        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['keyword'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 153
                yield "        </ul>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 147
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_20_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 158
    public function block_ezselection_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezselection_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezselection_field"));

        // line 159
        $___internal_parse_21_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 160
            yield "
    ";
            // line 161
            $context["options"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 161, $this->source); })()), "options", [], "any", false, false, false, 161);
            // line 162
            yield "
    ";
            // line 163
            if (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["fieldSettings"] ?? null), "multilingualOptions", [], "any", false, true, false, 163), CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 163, $this->source); })()), "languageCode", [], "any", false, false, false, 163), [], "array", true, true, false, 163)) {
                // line 164
                yield "        ";
                $context["options"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 164, $this->source); })()), "multilingualOptions", [], "any", false, false, false, 164), CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 164, $this->source); })()), "languageCode", [], "any", false, false, false, 164), [], "array", false, false, false, 164);
                // line 165
                yield "    ";
            } elseif (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["fieldSettings"] ?? null), "multilingualOptions", [], "any", false, true, false, 165), CoreExtension::getAttribute($this->env, $this->source, (isset($context["contentInfo"]) || array_key_exists("contentInfo", $context) ? $context["contentInfo"] : (function () { throw new RuntimeError('Variable "contentInfo" does not exist.', 165, $this->source); })()), "mainLanguageCode", [], "any", false, false, false, 165), [], "array", true, true, false, 165)) {
                // line 166
                yield "        ";
                $context["options"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 166, $this->source); })()), "multilingualOptions", [], "any", false, false, false, 166), CoreExtension::getAttribute($this->env, $this->source, (isset($context["contentInfo"]) || array_key_exists("contentInfo", $context) ? $context["contentInfo"] : (function () { throw new RuntimeError('Variable "contentInfo" does not exist.', 166, $this->source); })()), "mainLanguageCode", [], "any", false, false, false, 166), [], "array", false, false, false, 166);
                // line 167
                yield "    ";
            }
            // line 168
            yield "
    ";
            // line 169
            if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 169, $this->source); })()), "value", [], "any", false, false, false, 169), "selection", [], "any", false, false, false, 169)) <= 0)) {
                // line 170
                yield "    ";
            } elseif (CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 170, $this->source); })()), "isMultiple", [], "any", false, false, false, 170)) {
                // line 171
                yield "        <ul ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
        ";
                // line 172
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 172, $this->source); })()), "value", [], "any", false, false, false, 172), "selection", [], "any", false, false, false, 172));
                foreach ($context['_seq'] as $context["_key"] => $context["selectedIndex"]) {
                    // line 173
                    yield "            <li>";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["options"]) || array_key_exists("options", $context) ? $context["options"] : (function () { throw new RuntimeError('Variable "options" does not exist.', 173, $this->source); })()), $context["selectedIndex"], [], "array", false, false, false, 173), "html", null, true);
                    yield "</li>
        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['selectedIndex'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 175
                yield "        </ul>
    ";
            } else {
                // line 177
                yield "        ";
                $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["options"]) || array_key_exists("options", $context) ? $context["options"] : (function () { throw new RuntimeError('Variable "options" does not exist.', 177, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 177, $this->source); })()), "value", [], "any", false, false, false, 177), "selection", [], "any", false, false, false, 177), 0, [], "any", false, false, false, 177), [], "array", false, false, false, 177);
                // line 178
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("simple_block_field", $context, $blocks);
                yield "
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 159
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_21_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 187
    public function block_ezuser_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezuser_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezuser_field"));

        // line 188
        $___internal_parse_22_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 189
            yield "<dl ";
            yield from             $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
            yield ">
    <dt>User ID</dt>
    <dd>";
            // line 191
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 191, $this->source); })()), "value", [], "any", false, false, false, 191), "contentId", [], "any", false, false, false, 191), "html", null, true);
            yield "</dd>
    <dt>Username</dt>
    <dd>";
            // line 193
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 193, $this->source); })()), "value", [], "any", false, false, false, 193), "login", [], "any", false, false, false, 193), "html", null, true);
            yield "</dd>
    <dt>Email</dt>
    <dd><a href=\"mailto:";
            // line 195
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 195, $this->source); })()), "value", [], "any", false, false, false, 195), "email", [], "any", false, false, false, 195), "url"), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 195, $this->source); })()), "value", [], "any", false, false, false, 195), "email", [], "any", false, false, false, 195), "html", null, true);
            yield "</a></dd>
    <dt>Account status</dt>
    <dd>";
            // line 197
            yield ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 197, $this->source); })()), "value", [], "any", false, false, false, 197), "enabled", [], "any", false, false, false, 197)) ? ("enabled") : ("disabled"));
            yield "</dd>
</dl>
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 188
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_22_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 202
    public function block_ezbinaryfile_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezbinaryfile_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezbinaryfile_field"));

        // line 203
        $___internal_parse_23_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 204
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 204, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 204, $this->source); })()))) {
                // line 205
                yield "        ";
                $context["route_reference"] = $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension']->getRouteReference("ibexa.content.download", ["content" =>                 // line 206
(isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 206, $this->source); })()), "fieldIdentifier" => CoreExtension::getAttribute($this->env, $this->source,                 // line 207
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 207, $this->source); })()), "fieldDefIdentifier", [], "any", false, false, false, 207), "inLanguage" => CoreExtension::getAttribute($this->env, $this->source,                 // line 208
(isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 208, $this->source); })()), "prioritizedFieldLanguageCode", [], "any", false, false, false, 208), "version" => CoreExtension::getAttribute($this->env, $this->source,                 // line 209
(isset($context["versionInfo"]) || array_key_exists("versionInfo", $context) ? $context["versionInfo"] : (function () { throw new RuntimeError('Variable "versionInfo" does not exist.', 209, $this->source); })()), "versionNo", [], "any", false, false, false, 209)]);
                // line 211
                yield "        <a href=\"";
                yield $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension']->getPath((isset($context["route_reference"]) || array_key_exists("route_reference", $context) ? $context["route_reference"] : (function () { throw new RuntimeError('Variable "route_reference" does not exist.', 211, $this->source); })()));
                yield "\"
            ";
                // line 212
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 212, $this->source); })()), "value", [], "any", false, false, false, 212), "fileName", [], "any", false, false, false, 212), "html", null, true);
                yield "</a>&nbsp;(";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\FileSizeExtension']->sizeFilter(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 212, $this->source); })()), "value", [], "any", false, false, false, 212), "fileSize", [], "any", false, false, false, 212), 1), "html", null, true);
                yield ")
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 203
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_23_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 217
    public function block_ezmedia_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezmedia_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezmedia_field"));

        // line 218
        if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 218, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 218, $this->source); })()))) {
            // line 219
            $___internal_parse_24_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
                // line 220
                yield "    ";
                $context["type"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["fieldSettings"]) || array_key_exists("fieldSettings", $context) ? $context["fieldSettings"] : (function () { throw new RuntimeError('Variable "fieldSettings" does not exist.', 220, $this->source); })()), "mediaType", [], "any", false, false, false, 220);
                // line 221
                yield "    ";
                $context["value"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 221, $this->source); })()), "value", [], "any", false, false, false, 221);
                // line 222
                yield "    ";
                $context["route_reference"] = $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension']->getRouteReference("ibexa.content.download", ["content" =>                 // line 223
(isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 223, $this->source); })()), "fieldIdentifier" => CoreExtension::getAttribute($this->env, $this->source,                 // line 224
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 224, $this->source); })()), "fieldDefIdentifier", [], "any", false, false, false, 224), "version" => CoreExtension::getAttribute($this->env, $this->source,                 // line 225
(isset($context["versionInfo"]) || array_key_exists("versionInfo", $context) ? $context["versionInfo"] : (function () { throw new RuntimeError('Variable "versionInfo" does not exist.', 225, $this->source); })()), "versionNo", [], "any", false, false, false, 225)]);
                // line 227
                yield "    ";
                $context["download"] = $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension']->getPath((isset($context["route_reference"]) || array_key_exists("route_reference", $context) ? $context["route_reference"] : (function () { throw new RuntimeError('Variable "route_reference" does not exist.', 227, $this->source); })()));
                // line 228
                yield "    ";
                $context["width"] = (((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 228, $this->source); })()), "width", [], "any", false, false, false, 228) > 0)) ? ((("width=\"" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 228, $this->source); })()), "width", [], "any", false, false, false, 228)) . "\"")) : (""));
                // line 229
                yield "    ";
                $context["height"] = (((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 229, $this->source); })()), "height", [], "any", false, false, false, 229) > 0)) ? ((("height=\"" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 229, $this->source); })()), "height", [], "any", false, false, false, 229)) . "\"")) : (""));
                // line 230
                yield "    <div ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
    ";
                // line 232
                yield "    ";
                if ((((((isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 232, $this->source); })()) == "html5_video") || (                // line 233
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 233, $this->source); })()) == "quick_time")) || (                // line 234
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 234, $this->source); })()) == "windows_media_player")) || (                // line 235
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 235, $this->source); })()) == "real_player"))) {
                    // line 236
                    yield "        <video src=\"";
                    yield (isset($context["download"]) || array_key_exists("download", $context) ? $context["download"] : (function () { throw new RuntimeError('Variable "download" does not exist.', 236, $this->source); })());
                    yield "\" ";
                    yield (isset($context["width"]) || array_key_exists("width", $context) ? $context["width"] : (function () { throw new RuntimeError('Variable "width" does not exist.', 236, $this->source); })());
                    yield " ";
                    yield (isset($context["height"]) || array_key_exists("height", $context) ? $context["height"] : (function () { throw new RuntimeError('Variable "height" does not exist.', 236, $this->source); })());
                    yield "
            ";
                    // line 237
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 237, $this->source); })()), "autoplay", [], "any", false, false, false, 237)) ? ("autoplay=\"autoplay\"") : (""));
                    yield "
            ";
                    // line 238
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 238, $this->source); })()), "hasController", [], "any", false, false, false, 238)) ? ("controls=\"controls\"") : (""));
                    yield "
            ";
                    // line 239
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 239, $this->source); })()), "loop", [], "any", false, false, false, 239)) ? ("loop=\"loop\"") : (""));
                    yield ">
            Your browser does not support html5 video.
        </video>
    ";
                } elseif ((                // line 242
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 242, $this->source); })()) == "html5_audio")) {
                    // line 243
                    yield "        <audio src=\"";
                    yield (isset($context["download"]) || array_key_exists("download", $context) ? $context["download"] : (function () { throw new RuntimeError('Variable "download" does not exist.', 243, $this->source); })());
                    yield "\"
            ";
                    // line 244
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 244, $this->source); })()), "autoplay", [], "any", false, false, false, 244)) ? ("autoplay=\"autoplay\"") : (""));
                    yield "
            ";
                    // line 245
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 245, $this->source); })()), "hasController", [], "any", false, false, false, 245)) ? ("controls=\"controls\"") : (""));
                    yield "
            ";
                    // line 246
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 246, $this->source); })()), "loop", [], "any", false, false, false, 246)) ? ("loop=\"loop\"") : (""));
                    yield "
            preload=\"none\">
            Your browser does not support html5 audio.
        </audio>
    ";
                } elseif ((                // line 250
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 250, $this->source); })()) == "flash")) {
                    // line 251
                    yield "        <object type=\"application/x-shockwave-flash\" ";
                    yield (isset($context["width"]) || array_key_exists("width", $context) ? $context["width"] : (function () { throw new RuntimeError('Variable "width" does not exist.', 251, $this->source); })());
                    yield " ";
                    yield (isset($context["height"]) || array_key_exists("height", $context) ? $context["height"] : (function () { throw new RuntimeError('Variable "height" does not exist.', 251, $this->source); })());
                    yield " data=\"";
                    yield (isset($context["download"]) || array_key_exists("download", $context) ? $context["download"] : (function () { throw new RuntimeError('Variable "download" does not exist.', 251, $this->source); })());
                    yield "\">
            <param name=\"movie\" value=\"";
                    // line 252
                    yield (isset($context["download"]) || array_key_exists("download", $context) ? $context["download"] : (function () { throw new RuntimeError('Variable "download" does not exist.', 252, $this->source); })());
                    yield "\" />
            <param name=\"play\" value=\"";
                    // line 253
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 253, $this->source); })()), "autoplay", [], "any", false, false, false, 253)) ? ("true") : ("false"));
                    yield "\" />
            <param name=\"loop\" value=\"";
                    // line 254
                    yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 254, $this->source); })()), "loop", [], "any", false, false, false, 254)) ? ("true") : ("false"));
                    yield "\" />
            <param name=\"quality\" value=\"";
                    // line 255
                    yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 255, $this->source); })()), "quality", [], "any", false, false, false, 255);
                    yield "\" />
        </object>
    ";
                } elseif ((                // line 257
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 257, $this->source); })()) == "silverlight")) {
                    // line 258
                    yield "        <script type=\"text/javascript\">
            function onErrorHandler(sender, args) { }
            function onResizeHandler(sender, args) { }
        </script>
        <object data=\"data:application/x-silverlight,\" type=\"application/x-silverlight-2\" ";
                    // line 262
                    yield (isset($context["width"]) || array_key_exists("width", $context) ? $context["width"] : (function () { throw new RuntimeError('Variable "width" does not exist.', 262, $this->source); })());
                    yield " ";
                    yield (isset($context["height"]) || array_key_exists("height", $context) ? $context["height"] : (function () { throw new RuntimeError('Variable "height" does not exist.', 262, $this->source); })());
                    yield ">
            <param name=\"source\" value=\"";
                    // line 263
                    yield (isset($context["download"]) || array_key_exists("download", $context) ? $context["download"] : (function () { throw new RuntimeError('Variable "download" does not exist.', 263, $this->source); })());
                    yield "\" />
            <param name=\"onError\" value=\"onErrorHandler\" />
            <param name=\"onResize\" value=\"onResizeHandler\" />
            <a href=\"http://go.microsoft.com/fwlink/?LinkID=108182\">
                <img src=\"http://go.microsoft.com/fwlink/?LinkId=108181\" alt=\"Get Microsoft Silverlight\" />
            </a>
        </object>
        <iframe style=\"visibility: hidden; height: 0; width: 0; border: 0px;\"></iframe>
    ";
                }
                // line 272
                yield "    ";
                // line 273
                yield "    </div>
";
                return; yield '';
            })())) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 219
            yield Twig\Extension\CoreExtension::spaceless($___internal_parse_24_);
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 278
    public function block_ezobjectrelationlist_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelationlist_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelationlist_field"));

        // line 279
        $___internal_parse_25_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 280
            yield "    ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 280, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 280, $this->source); })()))) {
                // line 281
                yield "    <ul ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
        ";
                // line 282
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 282, $this->source); })()), "value", [], "any", false, false, false, 282), "destinationContentIds", [], "any", false, false, false, 282));
                foreach ($context['_seq'] as $context["_key"] => $context["contentId"]) {
                    // line 283
                    yield "            ";
                    if (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 283, $this->source); })()), "available", [], "any", false, false, false, 283), $context["contentId"], [], "array", false, false, false, 283)) {
                        // line 284
                        yield "                <li>
            ";
                        // line 285
                        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content::viewAction", ["contentId" => $context["contentId"], "viewType" => "embed", "layout" => false]));
                        yield "
        </li>";
                    }
                    // line 287
                    yield "        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['contentId'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 288
                yield "    </ul>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 279
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_25_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 296
    public function block_ezgmaplocation_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezgmaplocation_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezgmaplocation_field"));

        // line 313
        $___internal_parse_26_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 314
            yield "<div ";
            yield from             $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
            yield ">
    ";
            // line 315
            $context["defaultWidth"] = "500px";
            // line 316
            yield "    ";
            $context["defaultHeight"] = "200px";
            // line 317
            yield "    ";
            $context["defaultShowMap"] = true;
            // line 318
            yield "    ";
            $context["defaultShowInfo"] = true;
            // line 319
            yield "    ";
            $context["defaultDraggable"] = "true";
            // line 320
            yield "    ";
            $context["defaultScrollWheel"] = "true";
            // line 321
            yield "
    ";
            // line 322
            $context["hasContent"] =  !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 322, $this->source); })()), "value", [], "any", false, false, false, 322));
            // line 323
            yield "    ";
            $context["latitude"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 323, $this->source); })()), "value", [], "any", false, false, false, 323), "latitude", [], "any", false, false, false, 323);
            // line 324
            yield "    ";
            $context["longitude"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 324, $this->source); })()), "value", [], "any", false, false, false, 324), "longitude", [], "any", false, false, false, 324);
            // line 325
            yield "    ";
            $context["address"] = ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["field"] ?? null), "value", [], "any", false, true, false, 325), "address", [], "any", true, true, false, 325)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["field"] ?? null), "value", [], "any", false, true, false, 325), "address", [], "any", false, false, false, 325), "")) : (""));
            // line 326
            yield "    ";
            $context["mapId"] = ("maplocation-map-" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 326, $this->source); })()), "id", [], "any", false, false, false, 326));
            // line 327
            yield "
    ";
            // line 328
            $context["defaultZoom"] = ((((null === (isset($context["latitude"]) || array_key_exists("latitude", $context) ? $context["latitude"] : (function () { throw new RuntimeError('Variable "latitude" does not exist.', 328, $this->source); })())) && (null === (isset($context["longitude"]) || array_key_exists("longitude", $context) ? $context["longitude"] : (function () { throw new RuntimeError('Variable "longitude" does not exist.', 328, $this->source); })())))) ? (1) : (15));
            // line 329
            yield "
    ";
            // line 330
            $context["zoom"] = ((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "zoom", [], "any", true, true, false, 330)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "zoom", [], "any", false, false, false, 330), (isset($context["defaultZoom"]) || array_key_exists("defaultZoom", $context) ? $context["defaultZoom"] : (function () { throw new RuntimeError('Variable "defaultZoom" does not exist.', 330, $this->source); })()))) : ((isset($context["defaultZoom"]) || array_key_exists("defaultZoom", $context) ? $context["defaultZoom"] : (function () { throw new RuntimeError('Variable "defaultZoom" does not exist.', 330, $this->source); })())));
            // line 331
            yield "
    ";
            // line 332
            [$context["mapWidth"], $context["mapHeight"]] =             [(isset($context["defaultWidth"]) || array_key_exists("defaultWidth", $context) ? $context["defaultWidth"] : (function () { throw new RuntimeError('Variable "defaultWidth" does not exist.', 332, $this->source); })()), (isset($context["defaultHeight"]) || array_key_exists("defaultHeight", $context) ? $context["defaultHeight"] : (function () { throw new RuntimeError('Variable "defaultHeight" does not exist.', 332, $this->source); })())];
            // line 333
            yield "    ";
            if (CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "width", [], "any", true, true, false, 333)) {
                // line 334
                yield "        ";
                $context["mapWidth"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 334, $this->source); })()), "width", [], "any", false, false, false, 334);
                // line 335
                yield "    ";
            }
            // line 336
            yield "
    ";
            // line 337
            if (CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "height", [], "any", true, true, false, 337)) {
                // line 338
                yield "        ";
                $context["mapHeight"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 338, $this->source); })()), "height", [], "any", false, false, false, 338);
                // line 339
                yield "    ";
            }
            // line 340
            yield "
    ";
            // line 341
            $context["showMap"] = (isset($context["defaultShowMap"]) || array_key_exists("defaultShowMap", $context) ? $context["defaultShowMap"] : (function () { throw new RuntimeError('Variable "defaultShowMap" does not exist.', 341, $this->source); })());
            // line 342
            yield "    ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "showMap", [], "any", true, true, false, 342) &&  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 342, $this->source); })()), "showMap", [], "any", false, false, false, 342))) {
                // line 343
                yield "        ";
                $context["showMap"] = false;
                // line 344
                yield "    ";
            }
            // line 345
            yield "
    ";
            // line 346
            $context["showInfo"] = (isset($context["defaultShowInfo"]) || array_key_exists("defaultShowInfo", $context) ? $context["defaultShowInfo"] : (function () { throw new RuntimeError('Variable "defaultShowInfo" does not exist.', 346, $this->source); })());
            // line 347
            yield "    ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "showInfo", [], "any", true, true, false, 347) &&  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 347, $this->source); })()), "showInfo", [], "any", false, false, false, 347))) {
                // line 348
                yield "        ";
                $context["showInfo"] = false;
                // line 349
                yield "    ";
            }
            // line 350
            yield "
    ";
            // line 351
            $context["draggable"] = (isset($context["defaultDraggable"]) || array_key_exists("defaultDraggable", $context) ? $context["defaultDraggable"] : (function () { throw new RuntimeError('Variable "defaultDraggable" does not exist.', 351, $this->source); })());
            // line 352
            yield "    ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "draggable", [], "any", true, true, false, 352) &&  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 352, $this->source); })()), "draggable", [], "any", false, false, false, 352))) {
                // line 353
                yield "        ";
                $context["draggable"] = "false";
                // line 354
                yield "    ";
            }
            // line 355
            yield "
    ";
            // line 356
            $context["scrollWheel"] = (isset($context["defaultScrollWheel"]) || array_key_exists("defaultScrollWheel", $context) ? $context["defaultScrollWheel"] : (function () { throw new RuntimeError('Variable "defaultScrollWheel" does not exist.', 356, $this->source); })());
            // line 357
            yield "    ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "scrollWheel", [], "any", true, true, false, 357) &&  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 357, $this->source); })()), "scrollWheel", [], "any", false, false, false, 357))) {
                // line 358
                yield "        ";
                $context["scrollWheel"] = "false";
                // line 359
                yield "    ";
            }
            // line 360
            yield "
    ";
            // line 361
            if ((isset($context["showInfo"]) || array_key_exists("showInfo", $context) ? $context["showInfo"] : (function () { throw new RuntimeError('Variable "showInfo" does not exist.', 361, $this->source); })())) {
                // line 362
                yield "    <dl>
        <dt>Latitude</dt>
        <dd>";
                // line 364
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((( !(null === (isset($context["latitude"]) || array_key_exists("latitude", $context) ? $context["latitude"] : (function () { throw new RuntimeError('Variable "latitude" does not exist.', 364, $this->source); })()))) ? ((isset($context["latitude"]) || array_key_exists("latitude", $context) ? $context["latitude"] : (function () { throw new RuntimeError('Variable "latitude" does not exist.', 364, $this->source); })())) : (((($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("content-field.latitude.not_set", [], "ibexa_content_fields") == "content-field.latitude.not_set")) ? (Twig\Extension\CoreExtension::replace("Not set", [])) : ($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("content-field.latitude.not_set", [], "ibexa_content_fields"))))), "html", null, true);
                yield "</dd>
        <dt>Longitude</dt>
        <dd>";
                // line 366
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((( !(null === (isset($context["longitude"]) || array_key_exists("longitude", $context) ? $context["longitude"] : (function () { throw new RuntimeError('Variable "longitude" does not exist.', 366, $this->source); })()))) ? ((isset($context["longitude"]) || array_key_exists("longitude", $context) ? $context["longitude"] : (function () { throw new RuntimeError('Variable "longitude" does not exist.', 366, $this->source); })())) : (((($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("content-field.longitude.not_set", [], "ibexa_content_fields") == "content-field.longitude.not_set")) ? (Twig\Extension\CoreExtension::replace("Not set", [])) : ($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("content-field.longitude.not_set", [], "ibexa_content_fields"))))), "html", null, true);
                yield "</dd>
        ";
                // line 367
                if ((isset($context["address"]) || array_key_exists("address", $context) ? $context["address"] : (function () { throw new RuntimeError('Variable "address" does not exist.', 367, $this->source); })())) {
                    // line 368
                    yield "        <dt>Address</dt>
        <dd>";
                    // line 369
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["address"]) || array_key_exists("address", $context) ? $context["address"] : (function () { throw new RuntimeError('Variable "address" does not exist.', 369, $this->source); })()), "html", null, true);
                    yield "</dd>
        ";
                }
                // line 371
                yield "    </dl>
    ";
            }
            // line 373
            yield "
    ";
            // line 374
            if (((isset($context["hasContent"]) || array_key_exists("hasContent", $context) ? $context["hasContent"] : (function () { throw new RuntimeError('Variable "hasContent" does not exist.', 374, $this->source); })()) && (isset($context["showMap"]) || array_key_exists("showMap", $context) ? $context["showMap"] : (function () { throw new RuntimeError('Variable "showMap" does not exist.', 374, $this->source); })()))) {
                // line 375
                yield "    ";
                $context["latitude"] = ((array_key_exists("latitude", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["latitude"]) || array_key_exists("latitude", $context) ? $context["latitude"] : (function () { throw new RuntimeError('Variable "latitude" does not exist.', 375, $this->source); })()), 0)) : (0));
                // line 376
                yield "    ";
                $context["longitude"] = ((array_key_exists("longitude", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["longitude"]) || array_key_exists("longitude", $context) ? $context["longitude"] : (function () { throw new RuntimeError('Variable "longitude" does not exist.', 376, $this->source); })()), 0)) : (0));
                // line 377
                yield "        <script>
            if (typeof(window.ezgmaplocationMapsScriptLoaded) == 'undefined') {
                (function (win, doc) {
                    var myScript = document.createElement('script');
                    var myCss = document.createElement('link');
                    myScript.src = 'https://unpkg.com/leaflet@1.3.1/dist/leaflet.js';
                    myCss.rel = \"stylesheet\";
                    myCss.href = \"https://unpkg.com/leaflet@1.3.1/dist/leaflet.css\";
                    win.ezgmaplocationMapsScriptLoaded = true;
                    doc.body.appendChild(myCss);
                    doc.body.appendChild(myScript);
                })(window, document)
            }
        </script>
        <script type=\"text/javascript\">
            (function (win) {
                var mapView = function (mapId, latitude, longitude) {
                    var coordinates = [latitude, longitude];
                    var mapConfig = {
                        dragging: ";
                // line 396
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["draggable"]) || array_key_exists("draggable", $context) ? $context["draggable"] : (function () { throw new RuntimeError('Variable "draggable" does not exist.', 396, $this->source); })()), "html", null, true);
                yield ",
                        scrollWheelZoom: ";
                // line 397
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["scrollWheel"]) || array_key_exists("scrollWheel", $context) ? $context["scrollWheel"] : (function () { throw new RuntimeError('Variable "scrollWheel" does not exist.', 397, $this->source); })()), "html", null, true);
                yield ",
                        zoom: ";
                // line 398
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["zoom"]) || array_key_exists("zoom", $context) ? $context["zoom"] : (function () { throw new RuntimeError('Variable "zoom" does not exist.', 398, $this->source); })()), "html", null, true);
                yield ",
                        center: coordinates
                    };
                    var map = L.map(mapId, mapConfig);

                    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href=\"http://osm.org/copyright\">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker(coordinates).addTo(map);
                };
                win.addEventListener(
                    'load',
                    function () {
                        mapView(\"";
                // line 412
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["mapId"]) || array_key_exists("mapId", $context) ? $context["mapId"] : (function () { throw new RuntimeError('Variable "mapId" does not exist.', 412, $this->source); })()), "html", null, true);
                yield "\", ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["latitude"]) || array_key_exists("latitude", $context) ? $context["latitude"] : (function () { throw new RuntimeError('Variable "latitude" does not exist.', 412, $this->source); })()), "html", null, true);
                yield ", ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["longitude"]) || array_key_exists("longitude", $context) ? $context["longitude"] : (function () { throw new RuntimeError('Variable "longitude" does not exist.', 412, $this->source); })()), "html", null, true);
                yield ");
                    },
                    false
                );
            })(window);
        </script>
        ";
                // line 418
                $context["mapStyle"] = (((isset($context["mapWidth"]) || array_key_exists("mapWidth", $context) ? $context["mapWidth"] : (function () { throw new RuntimeError('Variable "mapWidth" does not exist.', 418, $this->source); })())) ? ((("width:" . (isset($context["mapWidth"]) || array_key_exists("mapWidth", $context) ? $context["mapWidth"] : (function () { throw new RuntimeError('Variable "mapWidth" does not exist.', 418, $this->source); })())) . ";")) : (""));
                // line 419
                yield "        ";
                $context["mapStyle"] = (((isset($context["mapHeight"]) || array_key_exists("mapHeight", $context) ? $context["mapHeight"] : (function () { throw new RuntimeError('Variable "mapHeight" does not exist.', 419, $this->source); })())) ? ((((isset($context["mapStyle"]) || array_key_exists("mapStyle", $context) ? $context["mapStyle"] : (function () { throw new RuntimeError('Variable "mapStyle" does not exist.', 419, $this->source); })()) . " height:") . (isset($context["mapHeight"]) || array_key_exists("mapHeight", $context) ? $context["mapHeight"] : (function () { throw new RuntimeError('Variable "mapHeight" does not exist.', 419, $this->source); })()))) : ((isset($context["mapStyle"]) || array_key_exists("mapStyle", $context) ? $context["mapStyle"] : (function () { throw new RuntimeError('Variable "mapStyle" does not exist.', 419, $this->source); })())));
                // line 420
                yield "        <div class=\"maplocation-map\" id=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["mapId"]) || array_key_exists("mapId", $context) ? $context["mapId"] : (function () { throw new RuntimeError('Variable "mapId" does not exist.', 420, $this->source); })()), "html", null, true);
                yield "\" style=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["mapStyle"]) || array_key_exists("mapStyle", $context) ? $context["mapStyle"] : (function () { throw new RuntimeError('Variable "mapStyle" does not exist.', 420, $this->source); })()), "html", null, true);
                yield "\"></div>
    ";
            }
            // line 422
            yield "</div>
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 313
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_26_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 432
    public function block_ezimage_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimage_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimage_field"));

        // line 433
        $___internal_parse_27_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 434
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 434, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 434, $this->source); })()))) {
                // line 435
                yield "<figure ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
    ";
                // line 436
                $context["imageAlias"] = $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ImageExtension']->getImageVariation((isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 436, $this->source); })()), (isset($context["versionInfo"]) || array_key_exists("versionInfo", $context) ? $context["versionInfo"] : (function () { throw new RuntimeError('Variable "versionInfo" does not exist.', 436, $this->source); })()), ((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "alias", [], "any", true, true, false, 436)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "alias", [], "any", false, false, false, 436), "original")) : ("original")));
                // line 437
                yield "    ";
                $context["src"] = (((isset($context["imageAlias"]) || array_key_exists("imageAlias", $context) ? $context["imageAlias"] : (function () { throw new RuntimeError('Variable "imageAlias" does not exist.', 437, $this->source); })())) ? ($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl(CoreExtension::getAttribute($this->env, $this->source, (isset($context["imageAlias"]) || array_key_exists("imageAlias", $context) ? $context["imageAlias"] : (function () { throw new RuntimeError('Variable "imageAlias" does not exist.', 437, $this->source); })()), "uri", [], "any", false, false, false, 437))) : ("//:0"));
                // line 438
                yield "    ";
                $context["attrs"] = ["class" => ((CoreExtension::getAttribute($this->env, $this->source,                 // line 439
($context["parameters"] ?? null), "class", [], "any", true, true, false, 439)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "class", [], "any", false, false, false, 439), "")) : ("")), "height" => ((CoreExtension::getAttribute($this->env, $this->source,                 // line 440
($context["parameters"] ?? null), "height", [], "any", true, true, false, 440)) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 440, $this->source); })()), "height", [], "any", false, false, false, 440)) : ((((isset($context["imageAlias"]) || array_key_exists("imageAlias", $context) ? $context["imageAlias"] : (function () { throw new RuntimeError('Variable "imageAlias" does not exist.', 440, $this->source); })())) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["imageAlias"]) || array_key_exists("imageAlias", $context) ? $context["imageAlias"] : (function () { throw new RuntimeError('Variable "imageAlias" does not exist.', 440, $this->source); })()), "height", [], "any", false, false, false, 440)) : ("")))), "width" => ((CoreExtension::getAttribute($this->env, $this->source,                 // line 441
($context["parameters"] ?? null), "width", [], "any", true, true, false, 441)) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 441, $this->source); })()), "width", [], "any", false, false, false, 441)) : ((((isset($context["imageAlias"]) || array_key_exists("imageAlias", $context) ? $context["imageAlias"] : (function () { throw new RuntimeError('Variable "imageAlias" does not exist.', 441, $this->source); })())) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["imageAlias"]) || array_key_exists("imageAlias", $context) ? $context["imageAlias"] : (function () { throw new RuntimeError('Variable "imageAlias" does not exist.', 441, $this->source); })()), "width", [], "any", false, false, false, 441)) : (""))))];
                // line 443
                yield "    ";
                if ( !Twig\Extension\CoreExtension::testEmpty(((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "ezlink", [], "any", true, true, false, 443)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "ezlink", [], "any", false, false, false, 443), [])) : ([])))) {
                    // line 444
                    yield "        <a
            href=\"";
                    // line 445
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 445, $this->source); })()), "ezlink", [], "any", false, false, false, 445), "href", [], "any", false, false, false, 445), "html", null, true);
                    yield "\"
            ";
                    // line 446
                    if (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "ezlink", [], "any", false, true, false, 446), "title", [], "any", true, true, false, 446)) {
                        yield " title=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 446, $this->source); })()), "ezlink", [], "any", false, false, false, 446), "title", [], "any", false, false, false, 446), "html_attr");
                        yield "\"";
                    }
                    // line 447
                    yield "            ";
                    if (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "ezlink", [], "any", false, true, false, 447), "target", [], "any", true, true, false, 447)) {
                        yield " target=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 447, $this->source); })()), "ezlink", [], "any", false, false, false, 447), "target", [], "any", false, false, false, 447), "html_attr");
                        yield "\"";
                    }
                    // line 448
                    yield "        >
    ";
                }
                // line 450
                yield "            <img src=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["src"]) || array_key_exists("src", $context) ? $context["src"] : (function () { throw new RuntimeError('Variable "src" does not exist.', 450, $this->source); })()), "html", null, true);
                yield "\" alt=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "alternativeText", [], "any", true, true, false, 450)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "alternativeText", [], "any", false, false, false, 450), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 450, $this->source); })()), "value", [], "any", false, false, false, 450), "alternativeText", [], "any", false, false, false, 450))) : (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 450, $this->source); })()), "value", [], "any", false, false, false, 450), "alternativeText", [], "any", false, false, false, 450))), "html", null, true);
                yield "\" ";
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable((isset($context["attrs"]) || array_key_exists("attrs", $context) ? $context["attrs"] : (function () { throw new RuntimeError('Variable "attrs" does not exist.', 450, $this->source); })()));
                foreach ($context['_seq'] as $context["attrname"] => $context["attrvalue"]) {
                    if ($context["attrvalue"]) {
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["attrname"], "html", null, true);
                        yield "=\"";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["attrvalue"], "html", null, true);
                        yield "\" ";
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                yield "/>
    ";
                // line 451
                if ( !Twig\Extension\CoreExtension::testEmpty(((CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "ezlink", [], "any", true, true, false, 451)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "ezlink", [], "any", false, false, false, 451), [])) : ([])))) {
                    // line 452
                    yield "        </a>
    ";
                }
                // line 454
                yield "</figure>
";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 433
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_27_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 459
    public function block_ezimageasset_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimageasset_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimageasset_field"));

        // line 460
        yield "    ";
        $___internal_parse_28_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 461
            yield "        ";
            if (( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 461, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 461, $this->source); })())) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 461, $this->source); })()), "available", [], "any", false, false, false, 461))) {
                // line 462
                yield "            <div ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
                ";
                // line 463
                yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content:embedAction", ["contentId" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                 // line 464
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 464, $this->source); })()), "value", [], "any", false, false, false, 464), "destinationContentId", [], "any", false, false, false, 464), "viewType" => "asset_image", "no_layout" => true, "params" => ["parameters" => Twig\Extension\CoreExtension::merge(((                // line 468
array_key_exists("parameters", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 468, $this->source); })()), ["alias" => "original"])) : (["alias" => "original"])), ["alternativeText" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 468, $this->source); })()), "value", [], "any", false, false, false, 468), "alternativeText", [], "any", false, false, false, 468)])]]));
                // line 470
                yield "
            </div>
        ";
            }
            // line 473
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 460
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_28_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 476
    public function block_ezobjectrelation_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelation_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelation_field"));

        // line 477
        $___internal_parse_29_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 478
            if (( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 478, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 478, $this->source); })())) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 478, $this->source); })()), "available", [], "any", false, false, false, 478))) {
                // line 479
                yield "    <div ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
        ";
                // line 480
                yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content::viewAction", ["contentId" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 480, $this->source); })()), "value", [], "any", false, false, false, 480), "destinationContentId", [], "any", false, false, false, 480), "viewType" => "text_linked", "layout" => false]));
                yield "
    </div>
";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 477
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_29_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 488
    public function block_simple_block_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "simple_block_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "simple_block_field"));

        // line 489
        $___internal_parse_30_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 490
            yield "    ";
            if ( !array_key_exists("field_value", $context)) {
                // line 491
                yield "        ";
                $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 491, $this->source); })()), "value", [], "any", false, false, false, 491);
                // line 492
                yield "    ";
            }
            // line 493
            yield "    <div ";
            yield from             $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
            yield ">
        ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 489
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_30_);
        // line 494
        yield (isset($context["field_value"]) || array_key_exists("field_value", $context) ? $context["field_value"] : (function () { throw new RuntimeError('Variable "field_value" does not exist.', 494, $this->source); })());
        $___internal_parse_31_ = ('' === $tmp = "    </div>
") ? '' : new Markup($tmp, $this->env->getCharset());
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_31_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 499
    public function block_simple_inline_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "simple_inline_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "simple_inline_field"));

        // line 500
        $___internal_parse_32_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 501
            yield "    ";
            if ( !array_key_exists("field_value", $context)) {
                // line 502
                yield "        ";
                $context["field_value"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 502, $this->source); })()), "value", [], "any", false, false, false, 502);
                // line 503
                yield "    ";
            }
            // line 504
            yield "    <span ";
            yield from             $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
            yield ">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["field_value"]) || array_key_exists("field_value", $context) ? $context["field_value"] : (function () { throw new RuntimeError('Variable "field_value" does not exist.', 504, $this->source); })()), "html", null, true);
            yield "</span>
";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 500
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_32_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 509
    public function block_field_attributes($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "field_attributes"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "field_attributes"));

        // line 510
        $___internal_parse_33_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 511
            yield "    ";
            $context["attr"] = ((array_key_exists("attr", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 511, $this->source); })()), [])) : ([]));
            // line 512
            yield "    ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 512, $this->source); })()));
            foreach ($context['_seq'] as $context["attrname"] => $context["attrvalue"]) {
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["attrname"], "html", null, true);
                yield "=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["attrvalue"], "html", null, true);
                yield "\" ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 510
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_33_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@IbexaCore/content_fields.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  1791 => 510,  1776 => 512,  1773 => 511,  1771 => 510,  1761 => 509,  1750 => 500,  1741 => 504,  1738 => 503,  1735 => 502,  1732 => 501,  1730 => 500,  1720 => 499,  1706 => 494,  1704 => 489,  1697 => 493,  1694 => 492,  1691 => 491,  1688 => 490,  1686 => 489,  1676 => 488,  1665 => 477,  1657 => 480,  1652 => 479,  1650 => 478,  1648 => 477,  1638 => 476,  1627 => 460,  1623 => 473,  1618 => 470,  1616 => 468,  1615 => 464,  1614 => 463,  1609 => 462,  1606 => 461,  1603 => 460,  1593 => 459,  1582 => 433,  1576 => 454,  1572 => 452,  1570 => 451,  1549 => 450,  1545 => 448,  1538 => 447,  1532 => 446,  1528 => 445,  1525 => 444,  1522 => 443,  1520 => 441,  1519 => 440,  1518 => 439,  1516 => 438,  1513 => 437,  1511 => 436,  1506 => 435,  1504 => 434,  1502 => 433,  1492 => 432,  1481 => 313,  1476 => 422,  1468 => 420,  1465 => 419,  1463 => 418,  1450 => 412,  1433 => 398,  1429 => 397,  1425 => 396,  1404 => 377,  1401 => 376,  1398 => 375,  1396 => 374,  1393 => 373,  1389 => 371,  1384 => 369,  1381 => 368,  1379 => 367,  1375 => 366,  1370 => 364,  1366 => 362,  1364 => 361,  1361 => 360,  1358 => 359,  1355 => 358,  1352 => 357,  1350 => 356,  1347 => 355,  1344 => 354,  1341 => 353,  1338 => 352,  1336 => 351,  1333 => 350,  1330 => 349,  1327 => 348,  1324 => 347,  1322 => 346,  1319 => 345,  1316 => 344,  1313 => 343,  1310 => 342,  1308 => 341,  1305 => 340,  1302 => 339,  1299 => 338,  1297 => 337,  1294 => 336,  1291 => 335,  1288 => 334,  1285 => 333,  1283 => 332,  1280 => 331,  1278 => 330,  1275 => 329,  1273 => 328,  1270 => 327,  1267 => 326,  1264 => 325,  1261 => 324,  1258 => 323,  1256 => 322,  1253 => 321,  1250 => 320,  1247 => 319,  1244 => 318,  1241 => 317,  1238 => 316,  1236 => 315,  1231 => 314,  1229 => 313,  1219 => 296,  1208 => 279,  1202 => 288,  1196 => 287,  1191 => 285,  1188 => 284,  1185 => 283,  1181 => 282,  1176 => 281,  1173 => 280,  1171 => 279,  1161 => 278,  1149 => 219,  1144 => 273,  1142 => 272,  1130 => 263,  1124 => 262,  1118 => 258,  1116 => 257,  1111 => 255,  1107 => 254,  1103 => 253,  1099 => 252,  1090 => 251,  1088 => 250,  1081 => 246,  1077 => 245,  1073 => 244,  1068 => 243,  1066 => 242,  1060 => 239,  1056 => 238,  1052 => 237,  1043 => 236,  1041 => 235,  1040 => 234,  1039 => 233,  1037 => 232,  1032 => 230,  1029 => 229,  1026 => 228,  1023 => 227,  1021 => 225,  1020 => 224,  1019 => 223,  1017 => 222,  1014 => 221,  1011 => 220,  1009 => 219,  1007 => 218,  997 => 217,  986 => 203,  975 => 212,  970 => 211,  968 => 209,  967 => 208,  966 => 207,  965 => 206,  963 => 205,  960 => 204,  958 => 203,  948 => 202,  937 => 188,  930 => 197,  923 => 195,  918 => 193,  913 => 191,  907 => 189,  905 => 188,  895 => 187,  884 => 159,  876 => 178,  873 => 177,  869 => 175,  860 => 173,  856 => 172,  851 => 171,  848 => 170,  846 => 169,  843 => 168,  840 => 167,  837 => 166,  834 => 165,  831 => 164,  829 => 163,  826 => 162,  824 => 161,  821 => 160,  819 => 159,  809 => 158,  798 => 147,  792 => 153,  783 => 151,  779 => 150,  774 => 149,  771 => 148,  769 => 147,  759 => 146,  748 => 140,  741 => 142,  738 => 141,  736 => 140,  726 => 139,  715 => 131,  706 => 134,  701 => 133,  698 => 132,  696 => 131,  686 => 130,  675 => 122,  667 => 125,  664 => 124,  661 => 123,  659 => 122,  649 => 121,  638 => 112,  630 => 115,  627 => 114,  624 => 113,  622 => 112,  612 => 111,  601 => 103,  589 => 106,  586 => 105,  583 => 104,  581 => 103,  571 => 102,  560 => 90,  552 => 97,  549 => 96,  546 => 95,  543 => 94,  540 => 93,  537 => 92,  534 => 91,  532 => 90,  522 => 89,  511 => 81,  503 => 84,  500 => 83,  497 => 82,  495 => 81,  485 => 80,  474 => 68,  466 => 75,  463 => 74,  460 => 73,  457 => 72,  454 => 71,  451 => 70,  448 => 69,  446 => 68,  436 => 67,  425 => 61,  418 => 63,  415 => 62,  413 => 61,  403 => 60,  392 => 42,  386 => 54,  377 => 52,  373 => 51,  368 => 50,  366 => 49,  363 => 48,  354 => 46,  350 => 45,  345 => 44,  342 => 43,  340 => 42,  330 => 41,  319 => 30,  313 => 36,  302 => 34,  298 => 33,  293 => 32,  290 => 31,  288 => 30,  278 => 29,  267 => 23,  260 => 25,  257 => 24,  255 => 23,  245 => 22,  234 => 16,  227 => 18,  224 => 17,  222 => 16,  212 => 15,  201 => 509,  198 => 507,  196 => 499,  193 => 498,  191 => 488,  188 => 485,  186 => 476,  183 => 475,  181 => 459,  178 => 458,  176 => 432,  173 => 425,  171 => 296,  168 => 292,  166 => 278,  163 => 277,  161 => 217,  158 => 216,  156 => 202,  153 => 201,  151 => 187,  148 => 182,  146 => 158,  143 => 157,  141 => 146,  138 => 145,  136 => 139,  133 => 138,  131 => 130,  128 => 129,  126 => 121,  123 => 119,  121 => 111,  118 => 110,  116 => 102,  113 => 101,  111 => 89,  108 => 88,  106 => 80,  103 => 79,  101 => 67,  98 => 66,  96 => 60,  93 => 58,  91 => 41,  88 => 40,  86 => 29,  83 => 28,  81 => 22,  78 => 21,  76 => 15,  73 => 14,  70 => 12,);
    }

    public function getSourceContext()
    {
        return new Source("{# Template blocks to be used by content fields #}
{# Block naming convention is <fieldDefinitionIdentifier>_field #}
{# Following variables are passed:
 # - \\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Field field the field to display
 # - \\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\ContentInfo contentInfo the contentInfo to which the field belongs to
 # - \\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\VersionInfo versionInfo the versionInfo to which the field belongs to
 # - mixed fieldSettings settings of the field (depends on the fieldtype)
 # - array parameters options passed to ez_render_field under the parameters key
 # - array attr the attributes to add the generate HTML, contains at least a \"class\" entry
 #              containing <fieldtypeidentifier>-field
 #}

{% trans_default_domain 'ibexa_content_fields' %}

{% block ezstring_field %}
{% apply spaceless %}
    {% set field_value = field.value.text %}
    {{ block( 'simple_inline_field' ) }}
{% endapply %}
{% endblock %}

{% block eztext_field %}
{% apply spaceless %}
    {% set field_value = field.value|nl2br %}
    {{ block( 'simple_block_field' ) }}
{% endapply %}
{% endblock %}

{% block ezauthor_field %}
{% apply spaceless %}
    {% if field.value.authors|length() > 0 %}
        <ul {{ block( 'field_attributes' ) }}>
        {% for author in field.value.authors %}
            <li><a href=\"mailto:{{ author.email|escape( 'url' ) }}\">{{ author.name }}</a></li>
        {% endfor %}
        </ul>
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezcountry_field %}
{% apply spaceless %}
    {% if fieldSettings.isMultiple and field.value.countries|length > 0 %}
        <ul {{ block( 'field_attributes' ) }}>
            {% for country in field.value.countries %}
                <li>{{ country['Name'] }}</li>
            {% endfor %}
        </ul>
    {% elseif field.value.countries|length() == 1 %}
        <p {{ block( 'field_attributes' ) }}>
        {% for country in field.value.countries %}
            {{ country['Name'] }}
        {% endfor %}
        </p>
    {% endif %}
{% endapply %}
{% endblock %}

{# @todo: add translate filter #}
{% block ezboolean_field %}
{% apply spaceless %}
    {% set field_value = field.value.bool ? 'Yes' : 'No' %}
    {{ block( 'simple_inline_field' ) }}
{% endapply %}
{% endblock %}

{% block ezdatetime_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% if fieldSettings.useSeconds %}
            {% set field_value = field.value.value|format_datetime( 'short', 'medium', locale=parameters.locale ) %}
        {% else %}
            {% set field_value = field.value.value|format_datetime( 'short', 'short', locale=parameters.locale ) %}
        {% endif %}
        {{ block( 'simple_block_field' ) }}
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezdate_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% set field_value = field.value.date|format_date( 'short', locale=parameters.locale, timezone='UTC' ) %}
        {{ block( 'simple_block_field' ) }}
    {% endif %}
{% endapply %}
{% endblock %}

{% block eztime_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% if fieldSettings.useSeconds %}
            {% set field_value = field.value.time|format_time( 'medium', locale=parameters.locale, timezone='UTC' ) %}
        {% else %}
            {% set field_value = field.value.time|format_time( 'short', locale=parameters.locale, timezone='UTC' ) %}
        {% endif %}
        {{ block( 'simple_block_field' ) }}
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezemail_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% set field_value = field.value.email %}
        <a href=\"mailto:{{ field.value.email|escape( 'url' ) }}\" {{ block( 'field_attributes' ) }}>{{ field.value.email }}</a>
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezinteger_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% set field_value = field.value.value %}
        {{ block( 'simple_inline_field' ) }}
    {% endif %}
{% endapply %}
{% endblock %}

{# @todo: handle localization #}
{% block ezfloat_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% set field_value = field.value.value %}
        {{ block( 'simple_inline_field' ) }}
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezurl_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        <a href=\"{{ field.value.link }}\"
            {{ block( 'field_attributes' ) }}>{{ field.value.text ? field.value.text : field.value.link }}</a>
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezisbn_field %}
{% apply spaceless %}
    {% set field_value = field.value.isbn %}
    {{ block( 'simple_inline_field' ) }}
{% endapply %}
{% endblock %}

{% block ezkeyword_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        <ul {{ block( 'field_attributes' ) }}>
        {% for keyword in field.value.values %}
            <li>{{ keyword }}</li>
        {% endfor %}
        </ul>
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezselection_field %}
{% apply spaceless %}

    {% set options = fieldSettings.options %}

    {% if fieldSettings.multilingualOptions[field.languageCode] is defined %}
        {% set options = fieldSettings.multilingualOptions[field.languageCode] %}
    {% elseif fieldSettings.multilingualOptions[contentInfo.mainLanguageCode] is defined %}
        {% set options = fieldSettings.multilingualOptions[contentInfo.mainLanguageCode] %}
    {% endif %}

    {% if field.value.selection|length() <= 0 %}
    {% elseif fieldSettings.isMultiple %}
        <ul {{ block( 'field_attributes' ) }}>
        {% for selectedIndex in field.value.selection %}
            <li>{{ options[selectedIndex] }}</li>
        {% endfor %}
        </ul>
    {% else %}
        {% set field_value = options[field.value.selection.0] %}
        {{ block( 'simple_block_field' ) }}
    {% endif %}
{% endapply %}
{% endblock %}

{# @todo:
 # - add translate filter
 # - legacy used to dump is_locked attribute
 #}
{% block ezuser_field %}
{% apply spaceless %}
<dl {{ block( 'field_attributes' ) }}>
    <dt>User ID</dt>
    <dd>{{ field.value.contentId }}</dd>
    <dt>Username</dt>
    <dd>{{ field.value.login }}</dd>
    <dt>Email</dt>
    <dd><a href=\"mailto:{{ field.value.email|escape( 'url' ) }}\">{{ field.value.email }}</a></dd>
    <dt>Account status</dt>
    <dd>{{ field.value.enabled ? 'enabled' : 'disabled' }}</dd>
</dl>
{% endapply %}
{% endblock %}

{% block ezbinaryfile_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
        {% set route_reference = ibexa_route( 'ibexa.content.download', {
            'content': content,
            'fieldIdentifier': field.fieldDefIdentifier,
            'inLanguage': content.prioritizedFieldLanguageCode,
            'version': versionInfo.versionNo
        } ) %}
        <a href=\"{{ ibexa_path( route_reference ) }}\"
            {{ block( 'field_attributes' ) }}>{{ field.value.fileName }}</a>&nbsp;({{ field.value.fileSize|ibexa_file_size( 1 ) }})
    {% endif %}
{% endapply %}
{% endblock %}

{% block ezmedia_field %}
{% if not ibexa_field_is_empty( content, field ) %}
{% apply spaceless %}
    {% set type = fieldSettings.mediaType %}
    {% set value = field.value %}
    {% set route_reference = ibexa_route( 'ibexa.content.download', {
        'content': content,
        'fieldIdentifier': field.fieldDefIdentifier,
        'version': versionInfo.versionNo
    } ) %}
    {% set download = ibexa_path( route_reference ) %}
    {% set width = value.width > 0 ? 'width=\"' ~ value.width ~ '\"' : \"\" %}
    {% set height = value.height > 0 ? 'height=\"' ~ value.height ~ '\"' : \"\" %}
    <div {{ block( 'field_attributes' ) }}>
    {% autoescape false %}
    {% if type == \"html5_video\"
        or type == \"quick_time\"
        or type == \"windows_media_player\"
        or type == \"real_player\" %}
        <video src=\"{{ download }}\" {{ width }} {{ height }}
            {{ value.autoplay ? 'autoplay=\"autoplay\"' : \"\" }}
            {{ value.hasController ? 'controls=\"controls\"' : \"\" }}
            {{ value.loop ? 'loop=\"loop\"' : \"\" }}>
            Your browser does not support html5 video.
        </video>
    {% elseif type == \"html5_audio\" %}
        <audio src=\"{{ download }}\"
            {{ value.autoplay ? 'autoplay=\"autoplay\"' : \"\" }}
            {{ value.hasController ? 'controls=\"controls\"' : \"\" }}
            {{ value.loop ? 'loop=\"loop\"' : \"\" }}
            preload=\"none\">
            Your browser does not support html5 audio.
        </audio>
    {% elseif type == 'flash' %}
        <object type=\"application/x-shockwave-flash\" {{ width }} {{ height }} data=\"{{ download }}\">
            <param name=\"movie\" value=\"{{ download }}\" />
            <param name=\"play\" value=\"{{ value.autoplay ? 'true' : 'false' }}\" />
            <param name=\"loop\" value=\"{{ value.loop ? 'true' : 'false' }}\" />
            <param name=\"quality\" value=\"{{ value.quality }}\" />
        </object>
    {% elseif type == 'silverlight' %}
        <script type=\"text/javascript\">
            function onErrorHandler(sender, args) { }
            function onResizeHandler(sender, args) { }
        </script>
        <object data=\"data:application/x-silverlight,\" type=\"application/x-silverlight-2\" {{ width }} {{ height }}>
            <param name=\"source\" value=\"{{ download }}\" />
            <param name=\"onError\" value=\"onErrorHandler\" />
            <param name=\"onResize\" value=\"onResizeHandler\" />
            <a href=\"http://go.microsoft.com/fwlink/?LinkID=108182\">
                <img src=\"http://go.microsoft.com/fwlink/?LinkId=108181\" alt=\"Get Microsoft Silverlight\" />
            </a>
        </object>
        <iframe style=\"visibility: hidden; height: 0; width: 0; border: 0px;\"></iframe>
    {% endif %}
    {% endautoescape %}
    </div>
{% endapply %}
{% endif %}
{% endblock %}

{% block ezobjectrelationlist_field %}
{% apply spaceless %}
    {% if not ibexa_field_is_empty( content, field ) %}
    <ul {{ block( 'field_attributes' ) }}>
        {% for contentId in field.value.destinationContentIds %}
            {% if parameters.available[contentId] %}
                <li>
            {{ render( controller( \"ibexa_content::viewAction\", {'contentId': contentId, 'viewType': 'embed', 'layout': false} ) ) }}
        </li>{% endif %}
        {% endfor %}
    </ul>
    {% endif %}
{% endapply %}
{% endblock %}

{# @todo:
 # - add translate filter
 #}
{% block ezgmaplocation_field %}
{##
 # This field type block accepts the following parameters:
 # - boolean showMap whether to show the map or not, default is true
 # - boolean showInfo whether to show the latitude, longitude and address or not, default is true
 # - integer zoom the default zoom level, default is 13
 # - boolean draggable whether to enable or not draggable map (useful on mobile / responsive layout), default is true
 # - string|false width the width of the rendered map with its unit (ie \"500px\" or \"50em\"),
 #                      set to false to not set any width style inline, default is 500px
 # - string|false height the height of the rendered map with its unit (ie \"200px\" or \"20em\"),
 #                         set to false to not set any height style inline, default is 200px
 # - boolean scrollWheel If false, disables scrollwheel zooming on the map. Enabled by default.
 #
 # For further reading:
 # - https://wiki.openstreetmap.org
 # - http://leafletjs.com/reference-1.3.0.html
 #}
{% apply spaceless %}
<div {{ block( 'field_attributes' ) }}>
    {% set defaultWidth = '500px' %}
    {% set defaultHeight = '200px' %}
    {% set defaultShowMap = true %}
    {% set defaultShowInfo = true %}
    {% set defaultDraggable = 'true' %}
    {% set defaultScrollWheel = 'true' %}

    {% set hasContent = field.value is not null %}
    {% set latitude = field.value.latitude %}
    {% set longitude = field.value.longitude %}
    {% set address = field.value.address|default( \"\" ) %}
    {% set mapId = \"maplocation-map-\" ~ field.id %}

    {% set defaultZoom = latitude is null and longitude is null ? 1 : 15 %}

    {% set zoom = parameters.zoom|default( defaultZoom ) %}

    {% set mapWidth, mapHeight = defaultWidth, defaultHeight %}
    {% if parameters.width is defined %}
        {% set mapWidth = parameters.width %}
    {% endif %}

    {% if parameters.height is defined %}
        {% set mapHeight = parameters.height %}
    {% endif %}

    {% set showMap = defaultShowMap %}
    {% if parameters.showMap is defined and not parameters.showMap %}
        {% set showMap = false %}
    {% endif %}

    {% set showInfo = defaultShowInfo %}
    {% if parameters.showInfo is defined and not parameters.showInfo %}
        {% set showInfo = false %}
    {% endif %}

    {% set draggable = defaultDraggable %}
    {% if parameters.draggable is defined and not parameters.draggable %}
        {% set draggable = 'false' %}
    {% endif %}

    {% set scrollWheel = defaultScrollWheel %}
    {% if parameters.scrollWheel is defined and not parameters.scrollWheel %}
        {% set scrollWheel = 'false' %}
    {% endif %}

    {% if showInfo %}
    <dl>
        <dt>Latitude</dt>
        <dd>{{ latitude is not null ? latitude : 'content-field.latitude.not_set'|trans|desc(\"Not set\") }}</dd>
        <dt>Longitude</dt>
        <dd>{{ longitude is not null ? longitude : 'content-field.longitude.not_set'|trans|desc(\"Not set\") }}</dd>
        {% if address %}
        <dt>Address</dt>
        <dd>{{ address }}</dd>
        {% endif %}
    </dl>
    {% endif %}

    {% if hasContent and showMap %}
    {% set latitude = latitude|default(0) %}
    {% set longitude = longitude|default(0) %}
        <script>
            if (typeof(window.ezgmaplocationMapsScriptLoaded) == 'undefined') {
                (function (win, doc) {
                    var myScript = document.createElement('script');
                    var myCss = document.createElement('link');
                    myScript.src = 'https://unpkg.com/leaflet@1.3.1/dist/leaflet.js';
                    myCss.rel = \"stylesheet\";
                    myCss.href = \"https://unpkg.com/leaflet@1.3.1/dist/leaflet.css\";
                    win.ezgmaplocationMapsScriptLoaded = true;
                    doc.body.appendChild(myCss);
                    doc.body.appendChild(myScript);
                })(window, document)
            }
        </script>
        <script type=\"text/javascript\">
            (function (win) {
                var mapView = function (mapId, latitude, longitude) {
                    var coordinates = [latitude, longitude];
                    var mapConfig = {
                        dragging: {{ draggable }},
                        scrollWheelZoom: {{ scrollWheel }},
                        zoom: {{ zoom }},
                        center: coordinates
                    };
                    var map = L.map(mapId, mapConfig);

                    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href=\"http://osm.org/copyright\">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker(coordinates).addTo(map);
                };
                win.addEventListener(
                    'load',
                    function () {
                        mapView(\"{{ mapId }}\", {{ latitude }}, {{ longitude }});
                    },
                    false
                );
            })(window);
        </script>
        {% set mapStyle = mapWidth ? \"width:\" ~ mapWidth  ~ \";\": \"\" %}
        {% set mapStyle = mapHeight ? mapStyle ~ \" height:\" ~ mapHeight : mapStyle %}
        <div class=\"maplocation-map\" id=\"{{ mapId }}\" style=\"{{ mapStyle }}\"></div>
    {% endif %}
</div>
{% endapply %}
{% endblock %}

{# This field accepts the following parameters:
 #   - alias (image variation name). Defaults to \"original\" (e.g. image originally uploaded)
 #   - parameters.width. Allows forcing width of the image in the HTML
 #   - parameters.height. Allows forcing height of the image in the HTML
 #   - parameters.class. Allows setting CSS custom class name for the image
 #}
{% block ezimage_field %}
{% apply spaceless %}
{% if not ibexa_field_is_empty( content, field ) %}
<figure {{ block( 'field_attributes' ) }}>
    {% set imageAlias = ibexa_image_alias( field, versionInfo, parameters.alias|default( 'original' ) ) %}
    {% set src = imageAlias ? asset( imageAlias.uri ) : \"//:0\" %}
    {% set attrs = {
        class: parameters.class|default(''),
        height: parameters.height is defined ? parameters.height : (imageAlias ? imageAlias.height : ''),
        width: parameters.width is defined ? parameters.width : (imageAlias ? imageAlias.width : ''),
    } %}
    {% if parameters.ezlink|default({}) is not empty %}
        <a
            href=\"{{ parameters.ezlink.href }}\"
            {% if parameters.ezlink.title is defined %} title=\"{{ parameters.ezlink.title|e('html_attr') }}\"{% endif %}
            {% if parameters.ezlink.target is defined %} target=\"{{ parameters.ezlink.target|e('html_attr') }}\"{% endif %}
        >
    {% endif %}
            <img src=\"{{ src }}\" alt=\"{{ parameters.alternativeText|default(field.value.alternativeText) }}\" {% for attrname, attrvalue in attrs %}{% if attrvalue %}{{ attrname }}=\"{{ attrvalue }}\" {% endif %}{% endfor %}/>
    {% if parameters.ezlink|default({}) is not empty %}
        </a>
    {% endif %}
</figure>
{% endif %}
{% endapply %}
{% endblock %}

{% block ezimageasset_field %}
    {% apply spaceless %}
        {% if not ibexa_field_is_empty(content, field) and parameters.available %}
            <div {{ block('field_attributes') }}>
                {{ render(controller('ibexa_content:embedAction', {
                    contentId: field.value.destinationContentId,
                    viewType: 'asset_image',
                    no_layout: true,
                    params: {
                        parameters: parameters|default({'alias': 'original'})|merge({'alternativeText': field.value.alternativeText })
                    }
                }))}}
            </div>
        {% endif %}
    {% endapply %}
{% endblock %}

{% block ezobjectrelation_field %}
{% apply spaceless %}
{% if not ibexa_field_is_empty( content, field ) and parameters.available %}
    <div {{ block( 'field_attributes' ) }}>
        {{ render( controller( \"ibexa_content::viewAction\", {'contentId': field.value.destinationContentId, 'viewType': 'text_linked', 'layout': false} ) ) }}
    </div>
{% endif %}
{% endapply %}
{% endblock %}

{# The simple_block_field block is a shorthand html block-based fields (like eztext) #}
{# You can define a field_value variable before rendering this one if you need special operation for rendering content (i.e. nl2br) #}
{% block simple_block_field %}
{% apply spaceless %}
    {% if field_value is not defined %}
        {% set field_value = field.value %}
    {% endif %}
    <div {{ block( 'field_attributes' ) }}>
        {% endapply %}{{ field_value|raw }}{% apply spaceless %}
    </div>
{% endapply %}
{% endblock %}

{% block simple_inline_field %}
{% apply spaceless %}
    {% if field_value is not defined %}
        {% set field_value = field.value %}
    {% endif %}
    <span {{ block( 'field_attributes' ) }}>{{ field_value }}</span>
{% endapply %}
{% endblock %}

{# Block for field attributes rendering. Useful to add a custom class, id or whatever HTML attribute to the field markup #}
{% block field_attributes %}
{% apply spaceless %}
    {% set attr = attr|default( {} ) %}
    {% for attrname, attrvalue in attr %}{{ attrname }}=\"{{ attrvalue }}\" {% endfor %}
{% endapply %}
{% endblock %}
", "@IbexaCore/content_fields.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/core/src/bundle/Core/Resources/views/content_fields.html.twig");
    }
}
