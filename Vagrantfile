VAGRANTFILE_API_VERSION = "2"

path = "#{File.dirname(__FILE__)}"

require 'yaml'
require path + '/.vg-scripts/homestead.rb'

Vagrant.configure( VAGRANTFILE_API_VERSION ) do |config|
  config.vm.define "homestead" do |homestead|
    Homestead.configure( homestead, YAML::load( File.read( path + '/.vg-scripts/Homestead.yaml' ) ) )
  end
end
