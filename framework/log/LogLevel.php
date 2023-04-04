<?php

namespace framework\log;

enum LogLevel {
    case DEBUG;
    case INFO;
    case WARN;
    case ERROR;
    case FATAL;
}