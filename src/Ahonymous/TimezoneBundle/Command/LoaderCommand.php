<?php

namespace Ahonymous\TimezoneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoaderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ahonymous:timezone:loader')
            ->setDescription('Load a timezone from Google Timezone API.')
            ->addArgument('timezone', InputArgument::OPTIONAL, 'PHP timezone name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $googleTimezone = $this->getContainer()->get('timezone');

        if ($timezoneKey = $input->getArgument('timezone')) {
            $name = $googleTimezone->getTimeZoneName($timezoneKey);
            $output->writeln(sprintf("\t%s is the name %s", $timezoneKey, $name));

            return;
        }

        foreach (timezone_identifiers_list() as $timezoneKey) {
            $name = $googleTimezone->getTimeZoneName($timezoneKey);
            // Google TimeZone API must be 5 request/sec
            sleep(0.2);
            $output->writeln(sprintf("\t%s is the name %s", $timezoneKey, $name));
        }
    }
}
