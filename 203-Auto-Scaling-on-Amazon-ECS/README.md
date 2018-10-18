# Auto Scaling on Amazon ECS

After learning how to create a service on Amazon ECS, we will learn how to auto scale service on Amazon ECS and use Amazon Elastic Load Balancer to forward requests.

## Prerequisites
* Make sure the region is **US East (N. Virginia)**, which its short name is **us-east-1**.

* Make sure you have created service on Amazon ECS by following [Get Started with Amazon ECS Service](../202-Get-Started-with-Amazon-ECS-Service/README.md).

## Auto Scale Service While Using Fargate
While using Fargate, we can scale the service to fit our requrement without taking care of instances.

* In Amazon ECS console, click **Clusters** on left panel. 

* Click **FargateCluster**.

* In tab **Services**, click **Create** button.





## Use EC2

### Use Fargate

### Use EC2

## Auto Scale Fargate on Amazon ECS

## Auto Scale EC2 Instances on Amazon ECS

## Create a Revision for EC2 Task
If you are using Fargate, please skip to next section. 

* In Amazon ECS, click **Task Definitions** on left panel.

* Select **runWebServerWithEC2** and click **Create new revision**.

* Step to **Container Definitions** part, and click container **my_web_server**.

* In **port mappings**, leave **Host port** as **blank**. Because each host port can only be used by a container at the same time. If we leave host port as blank, a randon host port will be assigned to container. In the later section, we will use Elastic Load Balancer to disperse and forward requests to containers with different host ports.