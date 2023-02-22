<?php

namespace Flixon\Routing\Annotations;

use Attribute;
use Symfony\Component\Routing\Annotation\Route as RouteBase;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route extends RouteBase { }