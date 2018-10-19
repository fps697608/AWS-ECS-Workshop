# Auto Scaling on Amazon ECS

After learning how to create a service on Amazon ECS, we will learn how to auto scale service on Amazon ECS and use Amazon Elastic Load Balancer to forward requests.

## Prerequisites
* Make sure the region is **US East (N. Virginia)**, which its short name is **us-east-1**.

* Make sure you have created service on Amazon ECS by following [Get Started with Amazon ECS Service](../202-Get-Started-with-Amazon-ECS-Service/README.md).

## Auto Scale Service While Using Fargate
While using Fargate, we can scale the service to fit our requrement without taking care of instances. In this section, we will create a service which can adjust the number of tasks automatically. The task we defined includes a container serving as a web server.

* In Amazon ECS console, click **Clusters** on left panel. 

* Click **FargateCluster**.

* In tab **Services**, click **Create** button.

* In **Launch type**, click **FARGATE**.

* In **Task Definition**, select **runWebServerWithFargate**.

* In **Service name**, type **AutoScalingFargateService**.

* In **Number of tasks**, type **1**.

* Click **Next step** button.

* In Cluster VPC, select **the VPC with CIDR 10.1.0.0/16** which you created in [Get Started with Amazon ECS Service](../202-Get-Started-with-Amazon-ECS-Service/README.md).

* In **Subnet**, add **both subnet**.

* Step to Load balancing part, select **Application Load Balancer**.

* Click [**EC2 Console**](https://us-east-1.console.aws.amazon.com/ec2/v2/home?region=us-east-1#SelectCreateELBWizard:) to create an Application Load Balancer.

![loadBalancer.png](../images/loadBalancer.png)

* In Application Load Balancer, click the **Create** button below.

* In **Name**, type **ECSFargateLoadBalancer**.

* Step to Availability Zones part, For VPC, select **the VPC with CIDR 10.1.0.0/16**.

* Select **both subnet**.

* Click **Next: Configure Security Settings** button.

* Click **Next: Configure Security Groups** button.

* In Step3: Configure Security Groups, select **Create a new security group**.

* In **Security group name**, type **AllowHTTPForLoadBalancer**.

* In **Type**, select **HTTP**.

* In **Source**, select **Anywhere**.

* Click **Next: Configure Routing**.

* In **Target group**, select **New target group**.

* In **Name**, Type **FargateContainers**.

* In **Protocol**, select **HTTP**.

* In **Target type**, select **ip**.

* In **Path**, type **/**.

* Click **Next: Register Targets**.

* Because we can set up the service to automatically register containers, therefore we click **Next: Review** to skip.

* Click **Create** and wait for the creation.

* Back to **Create Service** page.

* Click **Refresh** button, and select **ECSFargateLoadBalancer**.

![loadBalancer2.png](../images/loadBalancer2.png)

* Click **Add to load balancer** button.

* In **Listener Port**, select **80:HTTP**.

* In **Target group name**, select **FargateContainers** which we created before.

* Click **Next step** button.

* In **Service Auto Scaling**, select **Configure Service Auto Scaling to adjest your service's desired count**.

* In **Minimum number of tasks**, type **1**.

* In **Maximum number of tasks**, type **2**.

* In **IAM role for Service Auto Scaling**, select **Create new role**.

* For **Automatic task scaling policies** part, in **Scaling policy type**, select **Target tracking**.

* Type a name for **Policy name**.

* In **ECS service metric**, select **ALBRequestCountPerTarget**.

* In **Target value**, type **5**.

* Click **Next step**.

* Click **Create Service** and wait for creation.

* Click **View Service**.

* Go to [**EC2 console**](https://console.aws.amazon.com/ec2/v2/home?region=us-east-1), click **Load Balancers** on left panel.

* Click **ECSFargateLoadBalancer** which we created before, in **Description** tab, copy the **DNS name**.

![DNS.png](../images/DNS.png)

* Open a new tab in your browser, paste **the DNS name** and press Enter. You should be able to see the **hello world** message.

![browser3.png](../images/browser3.png)

* **Refresh** the page at least **5** times, and wait for a while.

* Back to ECS -> Clusters -> FargateCluster >  AutoScalingFargateService.

* You should be able to see that there are two tasks below.




## Use EC2

## Use Fargate

### Use EC2

## Auto Scale Fargate on Amazon ECS

## Auto Scale EC2 Instances on Amazon ECS

## Create a Revision for EC2 Task
If you are using Fargate, please skip to next section. 

* In Amazon ECS, click **Task Definitions** on left panel.

* Select **runWebServerWithEC2** and click **Create new revision**.

* Step to **Container Definitions** part, and click container **my_web_server**.

* In **port mappings**, leave **Host port** as **blank**. Because each host port can only be used by a container at the same time. If we leave host port as blank, a randon host port will be assigned to container. In the later section, we will use Elastic Load Balancer to disperse and forward requests to containers with different host ports.