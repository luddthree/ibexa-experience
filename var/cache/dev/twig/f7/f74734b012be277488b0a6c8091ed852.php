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

/* @ludvik/full/blog_post.html.twig */
class __TwigTemplate_ba19ff18187c305c1dc2114fdb53eb2a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@ibexadesign/pagelayout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/full/blog_post.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/full/blog_post.html.twig"));

        $this->parent = $this->loadTemplate("@ibexadesign/pagelayout.html.twig", "@ludvik/full/blog_post.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        // line 4
        yield "    <article>
        <!-- Container for the title and button -->
        <div class=\"title-container\">
            <!-- Round Button to go back to the homepage -->
            <div class=\"back-home\">
                <a href=\"/\" class=\"home-button\" title=\"Back to Homepage\">🠐</a>
            </div>
            
            <!-- Blog Title -->
            <h1>";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 13, $this->source); })()), "name", [], "any", false, false, false, 13), "html", null, true);
        yield "</h1>
        </div>

        <div class=\"blog-content\">
            ";
        // line 17
        yield $this->env->getFunction('ibexa_render_field')->getCallable()($this->env, (isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 17, $this->source); })()), "content");
        yield "
        </div>
    </article>

    <style>
        /* General styles for the article */
        article {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
        }

        /* Container for the title and button */
        .title-container {
            position: relative; /* Allows the button to be positioned inside this container */
            display: flex;
            align-items: center;
        }

        /* Header styles for the blog title */
        h1 {
            color: #333;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 2.5rem;
            text-align: center;
            margin-left: 60px; /* Create space for the button on the left */
            flex-grow: 1; /* Makes the title take the remaining space */
        }

        /* Button container and positioning */
        .back-home {
            position: absolute;
            left: 0; /* Position the button on the left */
            top: 50%; /* Vertically center the button */
            transform: translateY(-50%); /* Center alignment adjustment */
        }

        /* Round button styles */
        .home-button {
            display: inline-block;
            width: 50px;
            height: 50px;
            background-color: #007BFF;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        /* Hover effect for the round button */
        .home-button:hover {
            background-color: #0056b3;
        }

        /* Blog content styles */
        .blog-content {
            font-family: 'Arial', sans-serif;
            color: #666;
            line-height: 1.8;
            margin-top: 20px;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            article {
                padding: 10px;
                margin: 10px;
            }

            .home-button {
                width: 40px;
                height: 40px;
                line-height: 40px;
                font-size: 20px;
            }

            h1 {
                font-size: 2rem;
                margin-left: 50px; /* Adjust margin for smaller screens */
            }
        }
    </style>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/full/blog_post.html.twig";
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
        return array (  87 => 17,  80 => 13,  69 => 4,  59 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"@ibexadesign/pagelayout.html.twig\" %}

{% block content %}
    <article>
        <!-- Container for the title and button -->
        <div class=\"title-container\">
            <!-- Round Button to go back to the homepage -->
            <div class=\"back-home\">
                <a href=\"/\" class=\"home-button\" title=\"Back to Homepage\">🠐</a>
            </div>
            
            <!-- Blog Title -->
            <h1>{{ content.name }}</h1>
        </div>

        <div class=\"blog-content\">
            {{ ibexa_render_field(content, 'content') }}
        </div>
    </article>

    <style>
        /* General styles for the article */
        article {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
        }

        /* Container for the title and button */
        .title-container {
            position: relative; /* Allows the button to be positioned inside this container */
            display: flex;
            align-items: center;
        }

        /* Header styles for the blog title */
        h1 {
            color: #333;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 2.5rem;
            text-align: center;
            margin-left: 60px; /* Create space for the button on the left */
            flex-grow: 1; /* Makes the title take the remaining space */
        }

        /* Button container and positioning */
        .back-home {
            position: absolute;
            left: 0; /* Position the button on the left */
            top: 50%; /* Vertically center the button */
            transform: translateY(-50%); /* Center alignment adjustment */
        }

        /* Round button styles */
        .home-button {
            display: inline-block;
            width: 50px;
            height: 50px;
            background-color: #007BFF;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        /* Hover effect for the round button */
        .home-button:hover {
            background-color: #0056b3;
        }

        /* Blog content styles */
        .blog-content {
            font-family: 'Arial', sans-serif;
            color: #666;
            line-height: 1.8;
            margin-top: 20px;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            article {
                padding: 10px;
                margin: 10px;
            }

            .home-button {
                width: 40px;
                height: 40px;
                line-height: 40px;
                font-size: 20px;
            }

            h1 {
                font-size: 2rem;
                margin-left: 50px; /* Adjust margin for smaller screens */
            }
        }
    </style>
{% endblock %}
", "@ludvik/full/blog_post.html.twig", "/var/www/ibexa/blog/ibexa-experience/templates/themes/ludvik/full/blog_post.html.twig");
    }
}
